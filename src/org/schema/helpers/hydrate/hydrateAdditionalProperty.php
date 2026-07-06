<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use org\schema\PropertyValue;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array of PropertyValue objects.
 *
 * @param array|null $properties
 *
 * @return PropertyValue[]|null
 *
 * @throws ReflectionException
 */
function hydrateAdditionalProperty( ?array $properties = null ): ?array
{
    if (!is_array($properties) || !isIndexed($properties) || count($properties) === 0)
    {
        return null ;
    }

    return array_map( fn( $property ) => new PropertyValue( $property ) , $properties ) ;
}
