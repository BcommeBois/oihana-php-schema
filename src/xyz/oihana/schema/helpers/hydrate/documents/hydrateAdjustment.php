<?php

namespace xyz\oihana\schema\helpers\hydrate\documents;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use xyz\oihana\schema\business\documents\Adjustment;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array definition with the Adjustment class.
 *
 * Handles both a single adjustment array and an array of them — the second is the
 * usual shape, since a document commonly carries a carriage charge beside an
 * environmental fee.
 *
 * The adjustment is built through {@see Reflection::hydrate()} rather than the
 * {@see Adjustment} constructor : only that path honors the `#[HydrateAs]` /
 * `#[HydrateWith]` attributes declared on the class, so `amount` and `taxes` come
 * out typed. The resolution goes one level deeper on its own — each
 * {@see \xyz\oihana\schema\business\documents\TaxDetail} declares its own
 * `basisAmount` and `taxAmount`, and reflection recurses — so the whole
 * « what it costs / what it owes » pair is typed by this single call.
 *
 * 🔑 An **empty list is kept as an empty list**, where the rest of the family
 * answers `null`. « This document has no adjustment » is an answer, and it is not
 * the answer « nothing here was readable » : a consumer mapping over the value
 * deserves the empty list it can map over. A non-empty list that hydrates to
 * nothing keeps the family's `null` — there, nothing usable was found, which is
 * a different statement.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed $init Single adjustment data or array of adjustment data.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 *
 * @example
 * ```php
 * $adjustments = hydrateAdjustment
 * ([
 *     [
 *         'type'   => PriceComponentType::SHIPPING_FEE ,
 *         'amount' => [ 'value' => 52 , 'currency' => 'EUR' ] ,
 *         'taxes'  => [ [ 'rate' => 20 , 'taxAmount' => [ 'value' => 10.4 , 'currency' => 'EUR' ] ] ] ,
 *     ] ,
 * ]) ;
 *
 * $adjustments[ 0 ]->amount instanceof MonetaryAmount            ; // true
 * $adjustments[ 0 ]->taxes[ 0 ]->taxAmount instanceof MonetaryAmount ; // true
 * ```
 */
function hydrateAdjustment( mixed $init = null ) :mixed
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

        $adjustments = array_map
        (
            fn( $adjustment ) => hydrateAdjustment( $adjustment ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $adjustments , fn( $adjustment ) => $adjustment instanceof Adjustment || is_scalar( $adjustment ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    // The hydration plan is cached by the Reflection instance : keep it across calls so
    // a document costs one plan for all its adjustments, not one plan per adjustment.
    static $reflection = null ;

    $reflection ??= new Reflection() ;

    return $reflection->hydrate( $init , Adjustment::class ) ;
}
