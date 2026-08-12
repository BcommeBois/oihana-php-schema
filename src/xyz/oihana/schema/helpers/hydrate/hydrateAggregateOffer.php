<?php

namespace xyz\oihana\schema\helpers\hydrate;

use xyz\oihana\schema\organizations\Provider;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\products\PhysicalQuantity;

use org\schema\AggregateOffer;
use org\schema\QuantitativeValue;

use ReflectionException;

use function org\schema\helpers\hydrate\hydrateOfferPurchase;

/**
 * Hydrate an array definition with the AggregateOffer class.
 *
 * Use it in the 'products' definition in the DI container.
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

        // A PhysicalQuantity, so a packaging level keeps what it weighs and what
        // it occupies. A plain QuantitativeValue declares neither, and a class
        // discards the keys it does not declare : the weight would leave here
        // without an error, without a trace in the payload, and — since the
        // deeper levels stay raw arrays nobody rebuilds — only on the first
        // level, which is the hardest shape of all to account for afterwards.
        $eligibleQuantity = $offer->eligibleQuantity ?? null ;
        if( !( $eligibleQuantity instanceof QuantitativeValue ) && is_array( $eligibleQuantity ) )
        {
            $offer->eligibleQuantity = new PhysicalQuantity( $eligibleQuantity ) ;
        }

        $offers = $offer->offers ?? null ;
        if( is_array( $offers ) && !empty( $offers ) )
        {
            $offer->offers = array_values( array_filter( array_map
            (
                fn( $item ) => hydrateOfferPurchase( $item ) , $offers
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
