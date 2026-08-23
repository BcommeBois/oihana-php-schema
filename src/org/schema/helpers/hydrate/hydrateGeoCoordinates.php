<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use org\schema\GeoCoordinates;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array definition with the GeoCoordinates class.
 *
 * Handles both single GeoCoordinates array and array of GeoCoordinates.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed $init Single GeoCoordinates data or array of GeoCoordinates data
 *
 * @return mixed
 *
 * @throws ReflectionException
 */
function hydrateGeoCoordinates( mixed $init = null  ):mixed
{
    if ( !is_array( $init ) )
    {
        return $init ;
    }

    if ( isIndexed( $init ) )
    {
        $array = array_map
        (
            fn( $coordinates ) => hydrateGeoCoordinates( $coordinates ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $array , fn( $thing ) => $thing instanceof GeoCoordinates || is_scalar( $thing ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    return new GeoCoordinates( $init ) ;
}
