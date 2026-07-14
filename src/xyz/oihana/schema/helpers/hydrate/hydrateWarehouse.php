<?php

namespace xyz\oihana\schema\helpers\hydrate;

use org\schema\PostalAddress;
use ReflectionException;

use xyz\oihana\schema\organizations\Subsidiary;
use xyz\oihana\schema\places\Warehouse;

use function oihana\core\arrays\isIndexed;
use function org\schema\helpers\hydrate\hydratePostalAddress;

/**
 * Hydrate an array definition with the Warehouse class.
 *
 * @throws ReflectionException
 */
function hydrateWarehouse( mixed $init = null  ):mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if ( isIndexed( $init ) )
    {
        $warehouses = array_map
        (
            fn( $thing ) => hydrateWarehouse( $thing ) ,
            $init
        );

        $filtered = array_filter( $warehouses , fn( $thing ) => $thing instanceof Warehouse ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $warehouse = new Warehouse( $init ) ;

    if( ( $warehouse instanceof Warehouse ) )
    {
        $ownedBy = $warehouse->ownedBy ?? null ;
        if( !( $ownedBy instanceof Subsidiary ) && is_array( $ownedBy ) )
        {
            $warehouse->ownedBy = new Subsidiary( $ownedBy ) ;
        }

        $address = $warehouse->address ?? null ;
        if( !( $address instanceof PostalAddress ) && is_array( $address ) )
        {
            $warehouse->address = hydratePostalAddress( $address ) ;
        }
    }

    return $warehouse instanceof Warehouse ? $warehouse : null ;
}
