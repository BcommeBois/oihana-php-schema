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
 * `route` is hydrated only when the raw value is an array — when there is something
 * to hydrate. The helper's answer is then written as is, `null` included : an array
 * that resolves to nothing becomes `null`, never a leftover raw array.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
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

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $assignments , fn( $thing ) => $thing instanceof DeliveryRouteAssignment || is_scalar( $thing ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $assignment = new DeliveryRouteAssignment( $init ) ;

    $route = $assignment->route ?? null ;

    if ( is_array( $route ) )
    {
        $assignment->route = hydrateDefinedTerm( $route , DeliveryRouteTerm::class ) ;
    }

    return $assignment ;
}
