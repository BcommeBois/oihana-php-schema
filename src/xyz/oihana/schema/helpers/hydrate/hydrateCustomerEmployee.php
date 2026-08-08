<?php

namespace xyz\oihana\schema\helpers\hydrate;

use org\schema\DefinedTerm;
use ReflectionException;

use xyz\oihana\schema\people\CustomerEmployee;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateAdditionalProperty;
use function org\schema\helpers\hydrate\hydrateContactPoint;
use function org\schema\helpers\hydrate\hydrateDefinedTerm;

/**
 * Hydrate an array definition with the CustomerEmployee class.
 *
 * Handles both single employee array and array of employees.
 *
 * Each nested reference is hydrated only when the raw value is an array — when there is
 * something to hydrate. The helper's answer is then written as is, `null` included : an
 * array that resolves to nothing (an empty list, a list of unhydratable entries) becomes
 * `null`, never a leftover raw array, so the employee answers the same thing as the
 * nested helper called on its own. Anything that is not an array — an unresolved string
 * reference, an already typed instance — is left untouched.
 *
 * @param mixed $init Single employee data or array of employee data
 *
 * @return mixed
 *
 * @throws ReflectionException
 */
function hydrateCustomerEmployee( mixed $init = null  ):mixed
{
    if ( !is_array( $init ) )
    {
        return $init ;
    }

    if ( isIndexed( $init ) )
    {
        $employees = array_map
        (
            fn( $employee ) => hydrateCustomerEmployee( $employee ) ,
            $init
        );

        $filtered = array_filter( $employees , fn( $emp ) => $emp instanceof CustomerEmployee ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $employee = new CustomerEmployee( $init ) ;

    // ------- additionalProperty

    $additionalProperty = $employee->additionalProperty ?? null ;
    if ( is_array( $additionalProperty ) )
    {
        $employee->additionalProperty = hydrateAdditionalProperty( $additionalProperty ) ;
    }

    // ------- contactPoint

    $contactPoint = $employee->contactPoint ?? null ;
    if ( is_array( $contactPoint ) )
    {
        $employee->contactPoint = hydrateContactPoint( $contactPoint ) ;
    }

    // ------- jobTitle

    $jobTitle = $employee->jobTitle ?? null ;
    if ( is_array( $jobTitle ) )
    {
        $employee->jobTitle = hydrateDefinedTerm( $jobTitle ) ;
    }

    // ------- workLocation

    $workLocation = $employee->workLocation ?? null ;
    if ( is_array( $workLocation ) )
    {
        $employee->workLocation = hydrateCustomerSite( $workLocation ) ;
    }

    return $employee ;
}
