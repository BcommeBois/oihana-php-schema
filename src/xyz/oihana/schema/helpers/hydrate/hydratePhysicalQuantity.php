<?php

namespace xyz\oihana\schema\helpers\hydrate;

use ReflectionException;

use org\schema\QuantitativeValue;

use xyz\oihana\schema\products\PhysicalQuantity;

/**
 * Hydrate an array definition with the PhysicalQuantity class, down the whole
 * chain.
 *
 * A packaging chain is read one level at a time — the unit, then what one
 * package holds of it, then what one pallet holds of packages — and each level
 * carries its own weight and volume. Typing the head alone would leave a
 * consumer reading `->weight` at the first level and `['weight']` below it, on
 * a structure whose entire point is the **ratio between two levels**. So every
 * `valueReference` is walked and typed too.
 *
 * ⚠️ Only the constructor path needs this. `Reflection::hydrate()` types the
 * chain on its own, {@see PhysicalQuantity::$valueReference} declaring the
 * attribute for it.
 *
 * Use it in the 'products' definition in the DI container.
 *
 * @throws ReflectionException
 */
function hydratePhysicalQuantity( mixed $init = null ) :?PhysicalQuantity
{
    $quantity = null ;

    if( $init instanceof PhysicalQuantity )
    {
        $quantity = $init ;
    }
    else if( is_array( $init ) )
    {
        $quantity = new PhysicalQuantity( $init ) ;
    }

    if( $quantity instanceof PhysicalQuantity )
    {
        // A level already typed is left alone : re-wrapping it would rebuild what
        // a caller has, possibly, already enriched.
        $valueReference = $quantity->valueReference ?? null ;
        if( !( $valueReference instanceof QuantitativeValue ) && is_array( $valueReference ) )
        {
            $quantity->valueReference = hydratePhysicalQuantity( $valueReference ) ;
        }
    }

    return $quantity instanceof PhysicalQuantity ? $quantity : null ;
}
