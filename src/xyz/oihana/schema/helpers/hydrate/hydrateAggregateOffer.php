<?php

namespace xyz\oihana\schema\helpers\hydrate;

use xyz\oihana\schema\organizations\Provider;
use xyz\oihana\schema\places\Warehouse;

use org\schema\AggregateOffer;
use org\schema\QuantitativeValue;

use ReflectionException;

use function org\schema\helpers\hydrate\hydrateOfferPurchase;

/**
 * Hydrate an array definition with the AggregateOffer class.
 *
 * Use it in the 'products' definition in the DI container.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @throws ReflectionException
 */
function hydrateAggregateOffer( ?array $init = null  ):?AggregateOffer
{
    $offer = null ;

    if( is_array( $init ) )
    {
        $offer = new AggregateOffer( $init ) ;
    }

    if( ( $offer instanceof AggregateOffer ) )
    {
        $availableAtOrFrom = $offer->availableAtOrFrom ?? null ;
        if( !( $availableAtOrFrom instanceof Warehouse ) && is_array( $availableAtOrFrom ) )
        {
            $offer->availableAtOrFrom = hydrateWarehouse( $availableAtOrFrom ) ;
        }

        // PhysicalQuantity all the way down, so every packaging level keeps what
        // it weighs and what it occupies. A plain QuantitativeValue declares
        // neither, and a class discards the keys it does not declare : the weight
        // would leave here without an error and without a trace in the payload.
        // The whole chain, not only its head : a structure whose point is the
        // ratio between two levels cannot be read `->weight` on one and
        // `['weight']` on the next.
        $eligibleQuantity = $offer->eligibleQuantity ?? null ;
        if( !( $eligibleQuantity instanceof QuantitativeValue ) && is_array( $eligibleQuantity ) )
        {
            $offer->eligibleQuantity = hydratePhysicalQuantity( $eligibleQuantity ) ;
        }

        // An entry that is not an array is an unresolved reference — a handle, a code — and it
        // is kept as it stands. The guard belongs to the map here rather than to the filter :
        // hydrateOfferPurchase() answers `null` for anything it cannot build, so a reference
        // handed to it would be lost before the filter ever saw it.
        $offers = $offer->offers ?? null ;
        if( is_array( $offers ) && !empty( $offers ) )
        {
            $offer->offers = array_values( array_filter( array_map
            (
                fn( $item ) => is_scalar( $item ) ? $item : hydrateOfferPurchase( $item ) , $offers
            ))) ;
        }

        $provider = $offer->provider ?? null ;
        if( !( $provider instanceof Provider ) && is_array( $provider ) )
        {
            $offer->provider = new Provider( $provider ) ;
        }
    }

    return $offer instanceof AggregateOffer ? $offer : null ;
}
