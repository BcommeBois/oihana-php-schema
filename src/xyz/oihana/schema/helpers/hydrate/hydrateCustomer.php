<?php

namespace xyz\oihana\schema\helpers\hydrate;

use ReflectionException;

use xyz\oihana\schema\organizations\Customer;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateContactPoint;
use function org\schema\helpers\hydrate\hydratePostalAddress;

/**
 * Hydrate an array definition with the Customer class.
 *
 * Handles both a single customer array and an array of customers, and hydrates the
 * nested `contactPoint` and `address` references so the resolved customer matches the
 * shape served by the dedicated `/customers` endpoint (typed, null fields dropped).
 *
 * @param array|null $init Single customer data or array of customer data.
 *
 * @return mixed
 *
 * @throws ReflectionException
 */
function hydrateCustomer( mixed $init = null ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if ( isIndexed( $init ) )
    {
        $customers = array_map
        (
            fn( $customer ) => hydrateCustomer( $customer ) ,
            $init
        );

        $filtered = array_filter( $customers , fn( $customer ) => $customer instanceof Customer ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $customer = new Customer( $init ) ;

    // ------- contactPoint

    $contactPoint = hydrateContactPoint( $customer->contactPoint ?? null ) ;
    if ( $contactPoint !== null )
    {
        $customer->contactPoint = $contactPoint ;
    }

    // ------- address

    $address = hydratePostalAddress( $customer->address ?? null ) ;
    if ( $address !== null )
    {
        $customer->address = $address ;
    }

    return $customer ;
}
