<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use org\schema\PropertyValue;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array of PropertyValue objects.
 *
 * Anything that is not an array is handed back untouched — a lone
 * {@see PropertyValue} is a legal shape of the `additionalProperty` property,
 * and this helper is not the place to reject it. An array that is not an
 * indexed, non-empty list yields `null`, so a caller can tell "nothing to
 * hydrate" from a result.
 *
 * @param mixed $properties An indexed array of PropertyValue definitions, or any
 *                          value to pass through.
 *
 * @return PropertyValue[]|mixed|null
 *
 * @throws ReflectionException
 */
function hydrateAdditionalProperty( mixed $properties = null ): mixed
{
    if ( !is_array( $properties ) )
    {
        return $properties ;
    }

    if ( !isIndexed( $properties ) || count( $properties ) === 0 )
    {
        return null ;
    }

    return array_map( fn( $property ) => new PropertyValue( $property ) , $properties ) ;
}
