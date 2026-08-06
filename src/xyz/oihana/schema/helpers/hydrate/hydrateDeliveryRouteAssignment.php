<?php

namespace xyz\oihana\schema\helpers\hydrate;

use ReflectionException;

use xyz\oihana\schema\shipping\DeliveryRouteAssignment;
use xyz\oihana\schema\thesaurus\DeliveryRouteTerm;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateDefinedTerm;

/**
 * Hydrate an array definition with the DeliveryRouteAssignment class.
 *
 * Handles both a single assignment array and a list of them — the list being
 * the usual shape, since a same address is commonly served by more than one
 * route.
 *
 * The nested `route` is resolved into a {@see DeliveryRouteTerm} when it holds
 * the joined reference row. A bare code is left untouched : nothing has been
 * joined yet, and inventing a term out of a string would claim a label nobody
 * read.
 *
 * @param mixed $init Single DeliveryRouteAssignment data or array of DeliveryRouteAssignment data.
 *
 * @return mixed
 *
 * @throws ReflectionException
 */
function hydrateDeliveryRouteAssignment( mixed $init = null ) :mixed
{
    if ( !is_array( $init ) )
    {
        return $init ;
    }

    if ( isIndexed( $init ) )
    {
        $assignments = array_map
        (
            fn( $assignment ) => hydrateDeliveryRouteAssignment( $assignment ) ,
            $init
        );

        $filtered = array_filter( $assignments , fn( $thing ) => $thing instanceof DeliveryRouteAssignment ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $assignment = new DeliveryRouteAssignment( $init ) ;

    $route = hydrateDefinedTerm( $assignment->route ?? null , DeliveryRouteTerm::class ) ;

    if ( $route !== null )
    {
        $assignment->route = $route ;
    }

    return $assignment ;
}
