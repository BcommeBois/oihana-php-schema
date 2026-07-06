<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use org\schema\ContactPoint;
use org\schema\PropertyValue;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array of ContactPoint objects.
 *
 * @param array|null $properties
 *
 * @return PropertyValue[]|null
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
