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
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed                     $init  Single DefinedTerm data or array of DefinedTerm data.
 * @param class-string<DefinedTerm> $class The class to hydrate into. A subclass lets an enriched term keep the properties DefinedTerm
 *                                         does not declare.
 *
 * @return mixed
 *
 * @throws ReflectionException
 */
function hydrateDefinedTerm ( mixed $init = null , string $class = DefinedTerm::class ) :mixed
{
    if ( !is_array( $init ) )
    {
        return $init ;
    }

    if ( isIndexed( $init ) )
    {
        $terms = array_map
        (
            fn( $term ) => hydrateDefinedTerm( $term , $class ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $terms , fn( $thing ) => $thing instanceof DefinedTerm || is_scalar( $thing ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    return new $class( $init ) ;
}
