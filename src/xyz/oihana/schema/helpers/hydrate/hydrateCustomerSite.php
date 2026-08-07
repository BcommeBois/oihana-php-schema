<?php

namespace xyz\oihana\schema\helpers\hydrate;

use ReflectionException;

use xyz\oihana\schema\places\CustomerSite;

use xyz\oihana\schema\thesaurus\DeliveryMethodTerm;
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
 * Each nested reference is hydrated only when the raw value is an array — when there is
 * something to hydrate. The helper's answer is then written as is, `null` included : an
 * array that resolves to nothing (an empty list, a list of unhydratable entries) becomes
 * `null`, never a leftover raw array, so the site answers the same thing as the nested
 * helper called on its own. Anything that is not an array — an unresolved string
 * reference, an already typed instance — is left untouched.
 *
 * @param mixed $init Single CustomerSite data or array of CustomerSite data
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

    $additionalProperty = $site->additionalProperty ?? null ;
    if ( is_array( $additionalProperty ) )
    {
        $site->additionalProperty = hydrateAdditionalProperty( $additionalProperty ) ;
    }

    // ------- address

    $address = $site->address ?? null ;
    if ( is_array( $address ) )
    {
        $site->address = hydratePostalAddress( $address ) ;
    }

    // ------- geo

    $geo = $site->geo ?? null ;
    if ( is_array( $geo ) )
    {
        $site->geo = hydrateGeoCoordinates( $geo ) ;
    }

    // ------- deliveryMethod

    $deliveryMethod = $site->deliveryMethod ?? null ;
    if ( is_array( $deliveryMethod ) )
    {
        $site->deliveryMethod = hydrateDefinedTerm( $deliveryMethod , DeliveryMethodTerm::class ) ;
    }

    // ------- deliveryRoute

    $deliveryRoute = $site->deliveryRoute ?? null ;
    if ( is_array( $deliveryRoute ) )
    {
        $site->deliveryRoute = hydrateDeliveryRouteAssignment( $deliveryRoute ) ;
    }

    return $site ;
}
