<?php

namespace xyz\oihana\schema\helpers\hydrate\documents;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use xyz\oihana\schema\business\documents\BusinessDocumentLine;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array definition with the BusinessDocumentLine class.
 *
 * Handles both a single line array and an array of lines — the two shapes a
 * {@see \xyz\oihana\schema\business\documents\BusinessDocument} `documentLines`
 * payload can take.
 *
 * The line is built through {@see Reflection::hydrate()} rather than the
 * {@see BusinessDocumentLine} constructor : only that path honors the
 * `#[HydrateAs]` / `#[HydrateWith]` attributes declared on the class, so
 * `adjustments`, `price` (and its `priceComponent` breakdown), `quantity`,
 * `subtotal`, `taxes` and `total` come out typed instead of staying raw arrays.
 *
 * The `item` property is the exception : its `Product|Service` union cannot be
 * resolved from the property type alone, so it is delegated to
 * {@see hydrateDocumentLineItem()} which reads the payload's `@type`. That delegation
 * happens only when the raw payload holds an array under `item` — when there is
 * something to hydrate — and its answer is then written as is, `null` included : an
 * array that resolves to nothing becomes `null`, never a leftover raw array. Anything
 * else is left to whatever {@see Reflection::hydrate()} made of it.
 *
 * 🔑 An **empty list is kept as an empty list**, where the rest of the family
 * answers `null`. « This document has no line » is an answer, and it is not the
 * answer « nothing here was readable » : a consumer mapping over the value
 * deserves the empty list it can map over, and a document born empty is the
 * ordinary state of a draft rather than an anomaly. A non-empty list that
 * hydrates to nothing keeps the family's `null` — there, nothing usable was
 * found, which is a different statement.
 *
 * @param mixed $init Single line data or array of line data.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 */
function hydrateDocumentLine( mixed $init = null ) :mixed
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
            fn( $line ) => hydrateDocumentLine( $line ) ,
            $init
        );

        $filtered = array_values( array_filter( $lines , fn( $line ) => $line instanceof BusinessDocumentLine ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    // The hydration plan is cached by the Reflection instance : keep it across calls so
    // a full document costs one plan for all its lines, not one plan per line.
    static $reflection = null ;

    $reflection ??= new Reflection() ;

    $line = $reflection->hydrate( $init , BusinessDocumentLine::class ) ;

    // ------- item
    // Read from the raw payload : the union resolution done by hydrate() ignores the `@type`.

    $item = $init[ BusinessDocumentLine::ITEM ] ?? null ;
    if( is_array( $item ) )
    {
        $line->item = hydrateDocumentLineItem( $item ) ;
    }

    return $line ;
}
