<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use org\schema\enumerations\BusinessEntityType;
use org\schema\OfferForPurchase;

/**
 * Hydrate an OfferForPurchase from an array or instance.
 * @throws ReflectionException
 */
function hydrateOfferPurchase( mixed $data ): ?OfferForPurchase
{
    if( is_array( $data ) )
    {
        $data = new OfferForPurchase( $data ) ;
    }

    if( !( $data instanceof OfferForPurchase ) )
    {
        return null ;
    }

    $eligibleCustomerType = $data->eligibleCustomerType ?? null ;
    if( is_array( $eligibleCustomerType ) )
    {
        $data->eligibleCustomerType = new BusinessEntityType( $eligibleCustomerType ) ;
    }

    return $data ;
}
