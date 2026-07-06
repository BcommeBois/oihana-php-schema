<?php

namespace xyz\oihana\schema\helpers\hydrate;

use ReflectionException;

use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\products\StockLevel;

/**
 * Hydrate an array definition with the StockLevel class.
 *
 * Use it in the 'products' definition in the DI container.
 *
 * @throws ReflectionException
 */
function hydrateStockLevel( mixed $init = null  ):?StockLevel
{
    $level = null ;

    if( is_array( $init ) )
    {
        $level = new StockLevel( $init ) ;
    }

    if( ( $level instanceof StockLevel ) )
    {
        $assignedPOS = $level->assignedPOS ?? null ;
        if( !( $assignedPOS instanceof Warehouse ) && is_array( $assignedPOS ) )
        {
            $level->assignedPOS = new Warehouse( $assignedPOS ) ;
        }
    }

    return $level instanceof StockLevel ? $level : null ;
}
