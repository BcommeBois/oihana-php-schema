<?php

namespace xyz\oihana\schema\helpers\hydrate;

use ReflectionException;

use xyz\oihana\schema\places\CustomerSite;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateAdditionalProperty;
use function org\schema\helpers\hydrate\hydrateDefinedTerm;
use function org\schema\helpers\hydrate\hydrateGeoCoordinates;
use function org\schema\helpers\hydrate\hydratePostalAddress;

/**
 * Hydrate an array definition with the CustomerSite class.
 *
 * Handles both single CustomerSite array and array of CustomerSite things.
 *
 * @param array|null $init Single CustomerSite data or array of CustomerSite data
 *
 * @return mixed
 *
 * @throws ReflectionException
 */
function hydrateCustomerSite( mixed $init = null ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if ( isIndexed( $init ) )
    {
        $sites = array_map
        (
            fn( $thing ) => hydrateCustomerSite( $thing ) ,
            $init
        );

        $filtered = array_filter( $sites , fn( $thing ) => $thing instanceof CustomerSite ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $site = new CustomerSite( $init ) ;

    // ------- additionalProperty

    $additionalProperty = hydrateAdditionalProperty($site->additionalProperty ?? null ) ;
    if ( $additionalProperty !== null )
    {
        $site->additionalProperty = $additionalProperty;
    }

    // ------- contactPoint

    $address = hydratePostalAddress($site->address ?? null ) ;
    if ( $address !== null )
    {
        $site->address = $address;
    }

    // ------- geo

    $geo = hydrateGeoCoordinates($site->geo ?? null ) ;
    if ( $geo !== null )
    {
        $site->geo = $geo;
    }

    // ------- deliveryMethod

    $deliveryMethod = hydrateDefinedTerm($site->deliveryMethod ?? null ) ;
    if ( $deliveryMethod !== null )
    {
        $site->deliveryMethod = $deliveryMethod;
    }

    return $site ;
}
