<?php

namespace xyz\oihana\schema\helpers\hydrate\documents;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use xyz\oihana\schema\business\documents\DocumentTotals;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array definition with the DocumentTotals class.
 *
 * The totals are built through {@see Reflection::hydrate()} rather than the
 * {@see DocumentTotals} constructor : only that path honors the `#[HydrateAs]`
 * attributes declared on the class, so `allowanceTotal`, `balanceDue`,
 * `chargeTotal`, `prepaidAmount`, `subtotal`, `total` and `totalTax` come out as
 * {@see \org\schema\MonetaryAmount} instances instead of staying raw arrays. The
 * constructor assigns flat, and a flat assignment is exactly what leaves an
 * amount as `[ 'value' => 62.4 , 'currency' => 'EUR' ]`.
 *
 * Handles both a single totals array and an array of them — the second shape has
 * no consumer today, and is accepted all the same so the helper answers like
 * every other one of the family rather than being the one that throws a list
 * back at the caller.
 *
 * An empty array yields `null` : a document that carries no totals says so with
 * an absent value, not with an object of empty amounts.
 *
 * @param mixed $init Single totals data or array of totals data.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 *
 * @example
 * ```php
 * $totals = hydrateDocumentTotals
 * ([
 *     'subtotal' => [ 'value' => 100.0 , 'currency' => 'EUR' ] ,
 *     'total'    => [ 'value' => 120.0 , 'currency' => 'EUR' ] ,
 * ]) ;
 *
 * $totals->subtotal instanceof MonetaryAmount ; // true
 * ```
 */
function hydrateDocumentTotals( mixed $init = null ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $totals = array_map
        (
            fn( $total ) => hydrateDocumentTotals( $total ) ,
            $init
        );

        $filtered = array_values( array_filter( $totals , fn( $total ) => $total instanceof DocumentTotals ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    // The hydration plan is cached by the Reflection instance : keep it across calls so
    // a collection of documents costs one plan for all their totals, not one plan each.
    static $reflection = null ;

    $reflection ??= new Reflection() ;

    return $reflection->hydrate( $init , DocumentTotals::class ) ;
}
