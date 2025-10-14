<?php

namespace org\schema\traits ;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

use Spiral\JsonSchemaGenerator\Generator;

use oihana\reflect\traits\ReflectionTrait;
use org\schema\constants\Schema;


use function oihana\core\json\getJsonType;
use function oihana\reflect\helpers\getPublicProperties;

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

    use ReflectionTrait ;

    /**
     * JSON-LD @context declaration for Schema.org.
     */
    public const string CONTEXT = 'https://schema.org' ;

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
     * @param string $context
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
     * @param string|null $atType Optional JSON-LD type
     * @param string|null $atContext Optional JSON-LD context
     * @return $this
     */
    public function withJSONLDMeta( ?string $atType = null , ?string $atContext = null ) :static
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
     * @param string $type
     * @return $this
     */
    public function withAtType( string $type ) :static
    {
        $this->atType = $type;
        return $this ;
    }

    // -------

    /**
     * @var Generator|null
     */
    private static ?Generator $generator = null ;

    /**
     * Returns the internal Json-schema generator reference.
     * @return Generator
     */
    public static function getJsonSchemaGenerator():Generator
    {
        if( self::$generator === null )
        {
            self::$generator = new Generator();
        }
        return self::$generator ;
    }

    /**
     * Generate JSON Schema for a class statically.
     * @return array JSON Schema array representation
     */
    public static function jsonSchema(): array
    {
        return self::getJsonSchemaGenerator()
            ->generate(static::class )
            ->jsonSerialize() ;
    }

    /**
     * Generate JSON Schema for the current class.
     *
     * Returns a JSON Schema (draft-07) representation of the object structure,
     * including all public properties from the class, traits, and parent classes.
     *
     * @return array JSON Schema array representation
     *
     * @example
     * ```php
     * use org\schema\Place;
     *
     * $place = new Place();
     * $schema = $place->toJsonSchema();
     *
     * // Save schema to file
     * file_put_contents('place-schema.json', json_encode($schema, JSON_PRETTY_PRINT));
     * ```
     *
     * @example
     * ```php
     * // Generate schema from class (static context)
     * $schema = Place::toJsonSchema();
     * ```
     */
    public function toJsonSchema(): array
    {
        return self::getJsonSchemaGenerator()
                ->generate(static::class )
                ->jsonSerialize() ;
    }

    // /**
    //  * Validate data against a class schema statically.
    //  *
    //  * @param array $data Data to validate
    //  * @return array ['valid' => bool, 'errors' => array]
    //  */
    // public static function validate( array $data ): array
    // {
    //     $schema = static::jsonSchema();
    //     return self::validateAgainstSchema( $data , $schema ) ;
    // }
    //
    // /**
    //  * Validate data against the current class schema.
    //  *
    //  * @param array $data Data to validate
    //  * @return array ['valid' => bool, 'errors' => array]
    //  *
    //  * @example
    //  * ```php
    //  * use org\schema\Place;
    //  *
    //  * $place = new Place();
    //  * $data = [
    //  *     'name' => 'Tour Eiffel',
    //  *     'latitude' => 48.8584,
    //  *     'invalidProp' => 'test'
    //  * ];
    //  *
    //  * $result = $place->validateData($data);
    //  * if (!$result['valid']) {
    //  *     foreach ($result['errors'] as $error) {
    //  *         echo $error . "\n";
    //  *     }
    //  * }
    //  * ```
    //  */
    // public function validateData( array $data ): array
    // {
    //     $schema = $this->toJsonSchema();
    //     return self::validateAgainstSchema($data, $schema);
    // }

    //
    // /**
    //  * Internal method to generate JSON Schema from a class or object.
    //  *
    //  * @param string|object $classOrInstance
    //  * @param bool $strict
    //  * @return array
    //  * @throws ReflectionException
    //  */
    // private static function generateJsonSchema( string|object $classOrInstance , bool $strict ): array
    // {
    //     $instance        = is_object( $classOrInstance ) ? $classOrInstance : new static() ;
    //     $reflection      = $instance->reflection();
    //     $reflectionClass = $reflection->reflection( $classOrInstance) ;
    //
    //     $schema =
    //     [
    //         '$schema'              => 'http://json-schema.org/draft-07/schema#' ,
    //         'title'                => $reflectionClass->getShortName(),
    //         'type'                 => 'object',
    //         'properties'           => [],
    //         'additionalProperties' => !$strict
    //     ];
    //
    //     // Add description from docblock if available
    //     $docComment = $reflectionClass->getDocComment() ;
    //     if ( $docComment && preg_match('/@see\s+(https?:\/\/\S+)/' , $docComment , $matches ) )
    //     {
    //         $schema['$id'] = $matches[1] ;
    //     }
    //
    //     // Get all public properties including from traits and parent classes
    //     $properties = getPublicProperties( $reflectionClass ) ;
    //
    //     foreach ( $properties as $property )
    //     {
    //         $propertyName = $property->getName() ;
    //
    //         // Skip JSON-LD metadata
    //         if (
    //             $propertyName === 'CONTEXT' ||
    //             str_starts_with( $propertyName , 'atContext' ) ||
    //             str_starts_with( $propertyName , 'atType'    ) ||
    //             str_starts_with( $propertyName , '__'        )
    //         )
    //         {
    //             continue;
    //         }
    //
    //         $schema[ 'properties' ][ $propertyName ] = self::getPropertyJsonSchema( $property ) ;
    //     }
    //
    //     return $schema ;
    // }
    //
    // /**
    //  * Generate JSON Schema for a single property.
    //  *
    //  * @param ReflectionProperty $property
    //  * @return array
    //  */
    // private static function getPropertyJsonSchema( ReflectionProperty $property) :array
    // {
    //     $schema = [] ;
    //
    //     // Extract description from docblock
    //     $docComment = $property->getDocComment() ;
    //     if ( $docComment )
    //     {
    //         // Get first line of comment as description
    //         if ( preg_match('/\*\s*(.+?)(?:\n|\*\/)/s', $docComment, $matches ) )
    //         {
    //             $description = trim($matches[1]) ;
    //             if ( $description && !str_starts_with($description, '@' ) )
    //             {
    //                 $schema['description'] = $description;
    //             }
    //         }
    //     }
    //
    //     // Get type from property
    //     $type = $property->getType();
    //
    //     if ($type === null)
    //     {
    //         return array_merge( $schema , [ 'type' => ['string', 'number', 'boolean', 'object', 'array', 'null']]); // No type hint - allow any type
    //     }
    //
    //     if ($type instanceof ReflectionNamedType)
    //     {
    //         $schema = array_merge( $schema , self::mapPhpTypeToJsonSchema($type) ) ;
    //     }
    //     else if ( $type instanceof ReflectionUnionType )
    //     {
    //         $types = [];
    //         $hasNull = false;
    //
    //         foreach ($type->getTypes() as $unionType)
    //         {
    //             if ( $unionType->getName() === 'null' )
    //             {
    //                 $hasNull = true;
    //             }
    //             else
    //             {
    //                 $mapped = self::mapPhpTypeToJsonSchema($unionType) ;
    //                 if (isset($mapped['type']))
    //                 {
    //                     if (is_array($mapped['type']))
    //                     {
    //                         $types = array_merge( $types, $mapped['type'] ) ;
    //                     }
    //                     else
    //                     {
    //                         $types[] = $mapped['type'];
    //                     }
    //                 }
    //             }
    //         }
    //
    //         $types = array_values( array_unique( $types ) ) ; // Remove duplicates
    //
    //         if ( count($types) === 1)
    //         {
    //             $schema['type'] = $types[0] ;
    //         }
    //         else if (count($types) > 1)
    //         {
    //             $schema['type'] = $types ;
    //         }
    //
    //         if ($hasNull)
    //         {
    //             if ( isset($schema['type']) )
    //             {
    //                 if ( is_array($schema['type']) )
    //                 {
    //                     $schema['type'][] = 'null' ;
    //                 }
    //                 else
    //                 {
    //                     $schema['type'] = [$schema['type'], 'null'] ;
    //                 }
    //             }
    //             else
    //             {
    //                 $schema['type'] = 'null';
    //             }
    //         }
    //     }
    //
    //     return $schema ;
    // }
    //
    // /**
    //  * Map PHP type to JSON Schema type.
    //  *
    //  * @param  ReflectionNamedType $type
    //  * @return array
    //  */
    // private static function mapPhpTypeToJsonSchema( ReflectionNamedType $type ): array
    // {
    //     $schema   = [] ;
    //     $typeName = $type->getName() ;
    //
    //     $mapping =
    //     [
    //         'string' => 'string',
    //         'int'    => 'integer',
    //         'float'  => 'number',
    //         'bool'   => 'boolean',
    //         'array'  => 'array',
    //         'object' => 'object',
    //         'null'   => 'null',
    //         'mixed'  => ['string' , 'number' , 'boolean' , 'object' , 'array' , 'null' ]
    //     ];
    //
    //     if ( isset( $mapping[ $typeName] ) )
    //     {
    //         $schema['type'] = $mapping[$typeName];
    //     }
    //     else if ( class_exists( $typeName ) )
    //     {
    //         $schema['type'] = 'object'; // It's a class - represent as object
    //         $shortName = new ReflectionClass($typeName)->getShortName() ;
    //         if ( isset( $schema['description'] ) )
    //         {
    //             $schema['description'] .= " (Type: $shortName)";
    //         }
    //         else
    //         {
    //             $schema['description'] = "Type: $shortName";
    //         }
    //     }
    //     else
    //     {
    //         $schema['type'] = [ 'string' , 'number' , 'boolean' , 'object' , 'array' , 'null' ] ;
    //     }
    //
    //     return $schema;
    // }
    //
    // /**
    //  * Validate data against a JSON Schema.
    //  *
    //  * @param array $data
    //  * @param array $schema
    //  * @return array ['valid' => bool, 'errors' => array]
    //  */
    // private static function validateAgainstSchema( array $data , array $schema ) :array
    // {
    //     $errors = [];
    //
    //     if (!isset($schema['properties'])) {
    //         return ['valid' => true, 'errors' => []];
    //     }
    //
    //     foreach ($data as $key => $value)
    //     {
    //         if ( !isset( $schema['properties'][$key] ) ) // Check if property exists in schema
    //         {
    //             if ( isset($schema['additionalProperties']) && $schema['additionalProperties'] === false )
    //             {
    //                 $errors[] = "Property '$key' is not defined in schema";
    //             }
    //             continue ;
    //         }
    //
    //         $propertySchema = $schema['properties'][$key];
    //         $errors         = array_merge( $errors , self::validateValue( $value , $propertySchema , $key ) ) ;
    //     }
    //
    //     return
    //     [
    //         'valid'  => empty( $errors ) ,
    //         'errors' => $errors
    //     ];
    // }
    //
    // /**
    //  * Validate a single value against its schema.
    //  *
    //  * @param mixed $value
    //  * @param array $schema
    //  * @param string $path
    //  * @return array
    //  */
    // private static function validateValue( mixed $value , array $schema , string $path ) :array
    // {
    //     $errors = [];
    //
    //     if ( !isset( $schema['type'] ) )
    //     {
    //         return $errors;
    //     }
    //
    //     $expectedTypes = is_array( $schema['type'] ) ? $schema['type'] : [$schema['type'] ] ;
    //     $actualType    = getJsonType( $value  );
    //
    //     if (!in_array($actualType, $expectedTypes))
    //     {
    //         $typeList = implode(', ', $expectedTypes ) ;
    //         $errors[] = "Property '$path' should be of type [$typeList], got $actualType";
    //     }
    //
    //     return $errors;
    // }

}