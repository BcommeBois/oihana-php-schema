<?php

namespace org\schema\traits ;

use oihana\reflections\traits\ReflectionTrait;
use ReflectionException;

use org\schema\constants\Prop;

use function oihana\core\arrays\compress;

/**
 * Trait ThingTrait
 *
 * Provides common behavior for schema.org entities, including:
 * - Object hydration from array or object
 * - Automatic JSON-LD serialization
 * - Integration with internal reflection tools
 *
 * Used by all entities extending `org\schema\Thing`.
 */
trait ThingTrait
{
    /**
     * Constructor to hydrate public properties from an array or stdClass.
     *
     * This allows objects to be quickly populated with associative data
     * without manually setting each property.
     *
     * @param array|object|null $init A data array or object used to initialize the instance.
     *                                Keys must match public property names.
     *
     * @example
     * ```php
     * use org\schema\Person;
     * use org\schema\constants\Prop;
     *
     * $person = new Person
     * ([
     *     Prop::NAME => 'Jane Doe',
     *     Prop::URL  => 'https://example.com/janedoe'
     * ]);
     *
     * echo $person->name; // Outputs: Jane Doe
     * ```
     */
    public function __construct( array|object|null $init = null )
    {
        if( isset( $init ) )
        {
            foreach ( $init as $key => $value )
            {
                if( property_exists( $this , $key ) )
                {
                    $this->{ $key } = $value ;
                }
            }
        }
    }

    use ReflectionTrait ;

    /**
     * JSON-LD @context declaration for Schema.org.
     */
    public const string CONTEXT = 'https://schema.org' ;

    /**
     * Serializes the current object into a JSON-LD array.
     *
     * This method will include all public properties, the schema.org @context,
     * and the inferred @type based on the class name.
     *
     * Null values will be automatically removed using `compress()`.
     *
     * @return array A JSON-LD array representation of the object.
     *
     * @throws ReflectionException If reflection fails when accessing properties.
     *
     * @example
     * ```php
     * use org\schema\Person;
     * use org\schema\constants\Prop;
     *
     * $person = new Person([
     *     Prop::NAME => 'John Smith',
     *     Prop::ID   => 'jsmith-001'
     * ]);
     *
     * echo json_encode($person, JSON_PRETTY_PRINT);
     * ```
     * Output:
     * ```json
     * {
     *   "@type": "Person",
     *   "@context": "https://schema.org",
     *   "id": "jsmith-001",
     *   "name": "John Smith"
     * }
     * ```
     */
    public function jsonSerialize() : array
    {
        $object =
        [
            Prop::AT_TYPE    => $this->getClassName( $this ) ,
            Prop::AT_CONTEXT => static::CONTEXT
        ] ;

        $properties = $this->getPublicProperties( $this ) ;
        foreach( $properties as $property )
        {
            $name = $property->getName();
            $object[ $name ] = $this->{ $name } ?? null ;
        }

        return compress( $object ) ;
    }
}