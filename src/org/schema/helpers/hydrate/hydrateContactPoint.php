<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use org\schema\ContactPoint;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array of ContactPoint objects.
 *
 * Returns `null` — never an empty array — when the input is not an indexed,
 * non-empty array, so a caller can tell "nothing to hydrate" from a result.
 *
 * @param array|null $properties An indexed array of ContactPoint definitions.
 *
 * @return ContactPoint[]|null
 *
 * @throws ReflectionException
 */
function hydrateContactPoint( ?array $properties = null ): ?array
{
    if (!is_array( $properties ) || !isIndexed($properties) || count($properties) === 0)
    {
        return null ;
    }

    return array_map( fn( $property ) => new ContactPoint( $property ) , $properties ) ;
}
