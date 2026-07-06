<?php

namespace xyz\oihana\schema\helpers\hydrate;

use ReflectionException;

use xyz\oihana\schema\people\CustomerEmployee;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateAdditionalProperty;
use function org\schema\helpers\hydrate\hydrateContactPoint;

/**
 * Hydrate an array definition with the CustomerEmployee class.
 *
 * Handles both single employee array and array of employees.
 *
 * @param array|null $init Single employee data or array of employee data
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

    $additionalProperty = hydrateAdditionalProperty($employee->additionalProperty ?? null ) ;
    if ( $additionalProperty !== null )
    {
        $employee->additionalProperty = $additionalProperty;
    }

    // ------- contactPoint

    $contactPoint = hydrateContactPoint($employee->contactPoint ?? null ) ;
    if ( $contactPoint !== null )
    {
        $employee->contactPoint = $contactPoint;
    }

    // ------- workLocation

    $workLocation = hydrateCustomerSite($employee->workLocation ?? null ) ;
    if ( $workLocation !== null )
    {
        $employee->workLocation = $workLocation;
    }

    return $employee ;
}
