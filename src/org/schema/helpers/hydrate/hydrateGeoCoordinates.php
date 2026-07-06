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
 * @param array|null $init Single GeoCoordinates data or array of GeoCoordinates data
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

        $filtered = array_filter( $array , fn( $thing ) => $thing instanceof GeoCoordinates ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    return new GeoCoordinates( $init ) ;
}
