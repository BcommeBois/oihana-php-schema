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
 * {@see hydrateDocumentLineItem()} which reads the payload's `@type`.
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

    $item = hydrateDocumentLineItem( $init[ BusinessDocumentLine::ITEM ] ?? null ) ;
    if( $item !== null )
    {
        $line->item = $item ;
    }

    return $line ;
}
