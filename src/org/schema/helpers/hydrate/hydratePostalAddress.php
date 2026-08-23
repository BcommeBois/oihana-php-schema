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
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed $init Single PostalAddress data or array of PostalAddress data
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

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $addresses , fn( $thing ) => $thing instanceof PostalAddress || is_scalar( $thing ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $init = normalize( $init , CleanFlag::DEFAULT )  ;

    return is_array( $init ) ? new PostalAddress( $init ) : null ;
}
