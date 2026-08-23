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
 * nested `contactPoint` and `address` references so the resolved customer carries typed
 * values rather than raw arrays.
 *
 * Each nested reference is hydrated only when the raw value is an array — when there is
 * something to hydrate. The helper's answer is then written as is, `null` included : an
 * array that resolves to nothing (an empty list, a list of unhydratable entries) becomes
 * `null`, never a leftover raw array, so the customer answers the same thing as the
 * nested helper called on its own. Anything that is not an array — an unresolved string
 * reference, an already typed instance — is left untouched.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed $init Single customer data or array of customer data.
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

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $customers , fn( $customer ) => $customer instanceof Customer || is_scalar( $customer ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $customer = new Customer( $init ) ;

    // ------- contactPoint

    $contactPoint = $customer->contactPoint ?? null ;
    if ( is_array( $contactPoint ) )
    {
        $customer->contactPoint = hydrateContactPoint( $contactPoint ) ;
    }

    // ------- address

    $address = $customer->address ?? null ;
    if ( is_array( $address ) )
    {
        $customer->address = hydratePostalAddress( $address ) ;
    }

    return $customer ;
}
