<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use org\schema\DefinedTerm;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array definition with the DefinedTerm class.
 *
 * Handles both single DefinedTerm array and array of DefinedTerm.
 *
 * @param array|null $init Single DefinedTerm data or array of DefinedTerm data
 *
 * @return mixed
 *
 * @throws ReflectionException
 */
function hydrateDefinedTerm (mixed $init = null ) :mixed
{
    if ( !is_array( $init ) )
    {
        return $init ;
    }

    if ( isIndexed( $init ) )
    {
        $terms = array_map
        (
            fn( $term ) => hydrateDefinedTerm( $term ) ,
            $init
        );

        $filtered = array_filter( $terms , fn( $thing ) => $thing instanceof DefinedTerm ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    return new DefinedTerm( $init ) ;
}
