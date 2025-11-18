<?php

namespace org\schema\traits ;

use ReflectionException;

use oihana\reflect\traits\JsonSchemaTrait;

use org\schema\constants\Schema;

/**
 * Provides common behavior for schema.org entities, including:
 * - Object hydration from array or object
 * - Automatic JSON-LD serialization
 * - JSON Schema generation and validation
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
     * @throws ReflectionException
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
            $init = (array) $init ;

            if ( isset( $init[ Schema::AT_TYPE ] ) )
            {
                $this->atType = $init[ Schema::AT_TYPE ];
                unset( $init[ Schema::AT_TYPE ] ) ;
            }

            if ( isset( $init[ Schema::AT_CONTEXT ] ) )
            {
                $this->atContext = $init[ Schema::AT_CONTEXT ];
                unset( $init[ Schema::AT_CONTEXT ] );
            }

            $publicProperties = [];
            $properties       = $this->getPublicProperties( $this );

            foreach ( $properties as $property )
            {
                $publicProperties[ $property->getName() ] = true ;
            }

            foreach ( $init as $key => $value )
            {
                if ( isset( $publicProperties[ $key ] ) )
                {
                    $this->{ $key } = $value;
                }
            }
        }
    }

    use JsonSchemaTrait ;

    /**
     * The JSON-LD `@context` value.
     *
     * Default is `https://schema.org`.
     * @var string|null
     */
    private ?string $atContext = null;

    /**
     * The JSON-LD `@type` value.
     *
     * This can be manually set or automatically inferred from the class name.
     * @var string|null
     */
    private ?string $atType = null;

    /**
     * Serializes the current object into a JSON-LD array.
     *
     * Includes public properties, the JSON-LD `@context` and `@type`.
     * Null values are automatically removed.
     *
     * @return array JSON-LD representation of the object.
     *
     * @throws ReflectionException If reflection fails when accessing properties.
     *
     * @example
     * ```php
     * use org\schema\Person;
     * use org\schema\constants\Prop;
     *
     * $person = new Person
     * ([
     *     Prop::NAME => 'John Smith',
     *     Prop::ID   => 'jsmith-001'
     * ]);
     *
     * echo json_encode($person, JSON_PRETTY_PRINT);
     * ```
     * Output:
     * ```json
     * {
     *    "@type": "Person",
     *    "@context": "https://schema.org",
     *    "id": "jsmith-001",
     *    "name": "John Smith"
     * }
     * ```
     */
    public function jsonSerialize() : array
    {
        return
        [
            Schema::AT_TYPE    => $this->atType    ?? $this->getShortName( $this ) ,
            Schema::AT_CONTEXT => $this->atContext ?? static::CONTEXT ,
            ... $this->jsonSerializeFromPublicProperties( $this , true )
        ];
    }

    /**
     * Sets the internal JSON-LD `@context` attribute.
     *
     * Useful if you need a custom JSON-LD context.
     *
     * @param string $context Optional JSON-LD context.
     *
     * @return $this
     */
    public function withAtContext( string $context ) :static
    {
        $this->atContext = $context ;
        return $this ;
    }

    /**
     * Initializes both JSON-LD metadata: `@type` and `@context`.
     *
     * Can be called from constructor or later to override default values.
     *
     * @param string|null $atType    Optional JSON-LD type
     * @param string|null $atContext Optional JSON-LD context
     * @return $this
     */
    public function withJSONLDMeta
    (
        ?string $atType    = null ,
        ?string $atContext = null
    )
    :static
    {
        $this->atContext = $atContext ;
        $this->atType    = $atType ;
        return $this ;
    }

    /**
     * Sets the internal JSON-LD `@type` attribute.
     *
     * Allows overriding the default type inferred from the class.
     *
     * @param string $type Optional JSON-LD type
     * 
     * @return $this
     */
    public function withAtType( string $type ) :static
    {
        $this->atType = $type;
        return $this ;
    }
}