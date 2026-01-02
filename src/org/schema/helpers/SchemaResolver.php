<?php

namespace org\schema\helpers;

use function oihana\core\accessors\getKeyValue;

/**
 * Resolves a schema class name from a target object or array.
 *
 * The resolver inspects a given target (object or array), extracts a type value
 * using the provided key, and maps it to a schema class name.
 *
 * If the target is not an object or array, or if the resolved value is not a
 * non-empty string, `null` is returned.
 *
 * Typical usage includes dynamic schema resolution in data mappers,
 * document models, or hydration pipelines.
 */
final readonly class SchemaResolver
{
    /**
     * Creates a new SchemaResolver instance.
     *
     * @param string      $key      Key used to extract the schema type from the target (e.g. Prop::ADDITIONAL_TYPE).
     * @param array       $map      Associative map of schema types to class names.
     *                              Keys are schema type identifiers, values must be
     *                              fully-qualified class names.
     * @param string|null $default  Default schema class name to use when no mapping
     *                              matches. Must be a non-empty string or null.
     */
    public function __construct
    (
        private string  $key     ,
        private array   $map     ,
        private ?string $default = null
    )
    {}

    /**
     * Resolves the schema class name for the given target.
     *
     * @param mixed $target Object or array containing a schema type value.
     *
     * @return string|null Fully-qualified schema class name, or null if the target
     *                     is not resolvable or no valid mapping exists.
     */
    public function __invoke( mixed $target ) : ?string
    {
        if( !is_array( $target ) && !is_object( $target ) )
        {
            return null ;
        }

        $type  = getKeyValue( $target , $this->key ) ;
        $class = $this->map[ $type ] ?? $this->default ;

        return is_string( $class ) && $class !== '' && class_exists( $class ) ? $class : null ;
    }
}