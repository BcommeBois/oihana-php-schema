<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use org\schema\ContactPoint;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array of ContactPoint objects.
 *
 * Anything that is not an array is handed back untouched — a lone
 * {@see ContactPoint} or an unresolved string reference are legal shapes of the
 * `contactPoint` property, and this helper is not the place to reject them. An
 * array that is not an indexed, non-empty list yields `null`, so a caller can
 * tell "nothing to hydrate" from a result.
 *
 * @param mixed $properties An indexed array of ContactPoint definitions, or any
 *                          value to pass through.
 *
 * @return ContactPoint[]|mixed|null
 *
 * @throws ReflectionException
 */
function hydrateContactPoint( mixed $properties = null ): mixed
{
    if ( !is_array( $properties ) )
    {
        return $properties ;
    }

    if ( !isIndexed( $properties ) || count( $properties ) === 0 )
    {
        return null ;
    }

    return array_map( fn( $property ) => new ContactPoint( $property ) , $properties ) ;
}
