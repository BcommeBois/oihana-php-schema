<?php

namespace xyz\oihana\schema\helpers\hydrate;

use ReflectionException;

use org\schema\constants\Schema;
use org\schema\QuantitativeValue;

use xyz\oihana\schema\products\PhysicalQuantity;

use function oihana\core\objects\toAssociativeArray;

/**
 * Walk a packaging chain and hand back the level whose `additionalType` matches,
 * as a {@see PhysicalQuantity} — so the level found keeps what it weighs and
 * what it occupies.
 *
 * 🔑 **The level found is typed, and so is the chain below it.** A consumer that
 * walks on from the level it asked for reads `->weight` at every depth, never
 * `['weight']` one step down : the walk hands back the same shape
 * {@see \oihana\reflect\Reflection::hydrate()} builds, not a typed head sitting
 * over raw rows.
 *
 * The chain is read one level at a time, `valueReference` after
 * `valueReference`, until the type is met or the chain stops.
 *
 * 🔑 **The tree is a parameter, not a property.** The walk belongs to whoever
 * holds a chain, and a chain is not always reachable from the product that
 * defined it : it is built at import time, copied onto the offers, and it is
 * that copy which is stored — so a product read back from a base has none,
 * while the offer beside it does. Left private on the product, the walk had to
 * be written a second time by anyone holding the offer's tree, and a walk
 * written by hand rebuilds the levels as plain {@see QuantitativeValue} : that
 * class declares neither weight nor volume, and a class discards the keys it
 * does not declare. Both would leave without an error and without a trace.
 * Exposing the walk is what makes that loss impossible rather than repairable.
 *
 * ⚠️ Schema.org lets `valueReference` hold things that are not quantities at
 * all — a bare code, an enumeration. Anything that is neither an array nor a
 * {@see QuantitativeValue} ends the walk instead of being read as a level.
 *
 * Example:
 * ```php
 * $parcel = findPhysicalQuantityByType( UnitOfSaleType::PARCEL , $offer->eligibleQuantity ) ;
 *
 * $parcel?->weight ; // 245.1456
 * ```
 *
 * @param string $type One of the {@see \xyz\oihana\schema\enumerations\UnitOfSaleType} constants.
 * @param array<array-key,mixed>|QuantitativeValue|null $tree The chain to walk — a typed level, or the raw rows a base read leaves.
 *
 * @return PhysicalQuantity|null The matching level, or null when the chain holds none.
 *
 * @throws ReflectionException
 */
function findPhysicalQuantityByType( string $type , array|QuantitativeValue|null $tree = null ) :?PhysicalQuantity
{
    if( !$tree )
    {
        return null ;
    }

    if( $tree instanceof QuantitativeValue )
    {
        // An associative array whichever way the level came in, which also
        // settles the cache problem a typed level used to raise.
        $tree = toAssociativeArray( $tree ) ;
    }

    if( ( $tree[ Schema::ADDITIONAL_TYPE ] ?? null ) === $type )
    {
        // Always a fresh instance : the conversion above leaves no object to
        // hand back. Built through hydratePhysicalQuantity(), so the level
        // found is typed **and so is every level below it** — a weight reads
        // `->weight` at any depth, never `['weight']` one step down.
        return hydratePhysicalQuantity( $tree ) ;
    }

    $next = $tree[ Schema::VALUE_REFERENCE ] ?? null ;

    return is_array( $next ) || $next instanceof QuantitativeValue
         ? findPhysicalQuantityByType( $type , $next )
         : null ;
}
