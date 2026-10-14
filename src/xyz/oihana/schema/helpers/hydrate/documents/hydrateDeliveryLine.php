<?php

namespace xyz\oihana\schema\helpers\hydrate\documents;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use xyz\oihana\schema\business\documents\DeliveryLine;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array definition with the DeliveryLine class.
 *
 * Handles both a single line array and an array of lines — the two shapes a
 * {@see \xyz\oihana\schema\business\documents\DeliveryNote} `lines` payload can take.
 *
 * 🚨 **The list shape is the whole reason this function exists.**
 * {@see DeliveryLine} descends from `Thing`, so a constructor-based hydration folds a
 * whole list into a SINGLE object : `$lines[0]->position` then answers nothing, and no
 * error is raised anywhere. The element-by-element walk below is the only way in.
 *
 * The line itself is built through {@see Reflection::hydrate()} rather than the
 * constructor : only that path honors the `#[HydrateAs]` / `#[HydrateWith]`
 * attributes declared on the class, so the three quantities it reconciles —
 * `orderedQuantity`, `deliveredQuantity`, `backorderQuantity` — and the `weight` and
 * `volume` it takes from `HasPhysicalMeasures` come out typed instead of staying raw
 * arrays.
 *
 * 🔑 **The `item` needs no help**, and that was measured rather than assumed : the
 * `Product|Service` union carries `#[HydrateWith]`, and {@see Reflection::hydrate()}
 * resolves it from the payload's `@type` on its own — a payload saying `Service` gives
 * a {@see \org\schema\Service}, anything else the commerce-enriched product of this
 * package. Delegating it to {@see hydrateDocumentLineItem()} was written here first,
 * and a mutation proved the delegation changed nothing : it is left out rather than
 * kept as a line that looks load-bearing and is not.
 *
 * 🔑 **The order and the invoice a line answers to are left alone.** Each union names
 * a single class, which {@see Reflection::hydrate()} resolves on its own when a row
 * was joined — and a bare number is kept exactly as it was read, because a source
 * commonly states the number of an order it does not hold.
 *
 * 🔑 An **empty list is kept as an empty list**, where the rest of the family answers
 * `null`. « This note has no line » is an answer, and it is not the answer « nothing
 * here was readable » : a consumer mapping over the value deserves the empty list it
 * can map over. A non-empty list that hydrates to nothing keeps the family's `null` —
 * there, nothing usable was found, which is a different statement.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a
 * list of unresolved handles comes back as it stands, and only an entry that *was* an
 * array and resolved to nothing is dropped. The keys stay gap-free — a filtered list
 * left with holes serializes as a JSON object, and a consumer walking the value gets
 * something it cannot walk.
 *
 * @param mixed $init Single line data or array of line data.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 */
function hydrateDeliveryLine( mixed $init = null ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        if( count( $init ) === 0 )
        {
            return $init ;
        }

        $lines = array_map
        (
            fn( $line ) => hydrateDeliveryLine( $line ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $lines , fn( $line ) => $line instanceof DeliveryLine || is_scalar( $line ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    // The hydration plan is cached by the Reflection instance : keep it across calls so
    // a full note costs one plan for all its lines, not one plan per line.
    static $reflection = null ;

    $reflection ??= new Reflection() ;

    // The `item` union is resolved by the reflection itself, from the `@type` — see
    // the note above. Nothing is left to do here.
    return $reflection->hydrate( $init , DeliveryLine::class ) ;
}
