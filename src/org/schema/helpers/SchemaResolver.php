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
     * The discriminator value (read at {@see $key}) may be a single string or an
     * array of types. When it is an array (a multi-typed document), the class is
     * resolved by {@see $map} declaration order — see {@see resolveFromList()}.
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

        $class = is_array( $type )
               ? $this->resolveFromList( $type )
               : ( $this->map[ $type ] ?? $this->default ) ;

        return is_string( $class ) && $class !== '' && class_exists( $class ) ? $class : null ;
    }

    /**
     * Resolves the schema class from a list of candidate types, honouring the
     * declaration order of {@see $map} as the resolution priority — so a document
     * carrying several types resolves deterministically, regardless of the order
     * the types appear in the document. Falls back to {@see $default} when none
     * of the candidates is mapped (including an empty list).
     *
     * @param array<int,mixed> $types The candidate type values.
     *
     * @return string|null The mapped class, the default, or null.
     */
    private function resolveFromList( array $types ) : ?string
    {
        foreach( $this->map as $type => $class )
        {
            if( in_array( $type , $types , true ) )
            {
                return $class ;
            }
        }

        return $this->default ;
    }
}