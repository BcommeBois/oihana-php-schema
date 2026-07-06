<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use oihana\core\arrays\CleanFlag;
use org\schema\PostalAddress;

use function oihana\core\arrays\isIndexed;
use function oihana\core\normalize;

/**
 * Hydrate an array definition with the PostalAddress class.
 *
 * Handles both single PostalAddress array and array of PostalAddress.
 *
 * @param array|null $init Single PostalAddress data or array of PostalAddress data
 *
 * @return mixed
 *
 * @throws ReflectionException
 */
function hydratePostalAddress( mixed $init = null  ):mixed
{
    if ( !is_array( $init ) )
    {
        return $init ;
    }

    if ( isIndexed( $init ) )
    {
        $addresses = array_map
        (
            fn( $address ) => hydratePostalAddress( $address ) ,
            $init
        );

        $filtered = array_filter( $addresses , fn( $thing ) => $thing instanceof PostalAddress ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $init = normalize( $init , CleanFlag::DEFAULT )  ;

    return is_array( $init ) ? new PostalAddress( $init ) : null ;
}
