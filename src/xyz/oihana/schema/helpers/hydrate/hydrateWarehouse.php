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
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
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

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $warehouses , fn( $thing ) => $thing instanceof Warehouse || is_scalar( $thing ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $warehouse = new Warehouse( $init ) ;

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

    return $warehouse ;
}
