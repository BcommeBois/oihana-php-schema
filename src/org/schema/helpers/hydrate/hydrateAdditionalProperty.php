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
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
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

    // An entry that is not an array is an unresolved reference — a handle, a code — and it is
    // kept as it stands, exactly as a lone one is by the guard above. Handing it to the
    // constructor threw : `PropertyValue` takes an array or an object, never a string.
    return array_map( fn( $property ) => is_scalar( $property ) ? $property : new PropertyValue( $property ) , $properties ) ;
}
