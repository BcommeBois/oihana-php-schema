<?php

namespace xyz\oihana\schema\helpers\hydrate\documents;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;
use org\schema\Service;

use xyz\oihana\schema\products\Product;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array definition with the product or service class of a document line item.
 *
 * The `item` of a {@see \xyz\oihana\schema\business\documents\BusinessDocumentLine} is
 * typed as a `Product|Service` union, which no property type can resolve on its own :
 * the target class is therefore read from the payload's JSON-LD `@type`.
 *
 * - a `@type` ending with `Service` (e.g. `Service`, `FoodService`) gives a {@see Service} ;
 * - anything else gives a {@see Product} — the commerce-enriched product of this package,
 *   which is a `org\schema\Product` and so satisfies the line's declared type.
 *
 * @param mixed $init Single item data or array of item data.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 */
function hydrateDocumentLineItem( mixed $init = null ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $items = array_map
        (
            fn( $item ) => hydrateDocumentLineItem( $item ) ,
            $init
        );

        $filtered = array_values( array_filter( $items , fn( $item ) => $item instanceof Product || $item instanceof Service ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    static $reflection = null ;

    $reflection ??= new Reflection() ;

    $type = $init[ Schema::AT_TYPE ] ?? null ;

    $class = is_string( $type ) && str_ends_with( strtolower( $type ) , 'service' )
           ? Service::class
           : Product::class ;

    return $reflection->hydrate( $init , $class ) ;
}
