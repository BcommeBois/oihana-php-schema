<?php

namespace org\schema\traits ;

use oihana\reflections\traits\ReflectionTrait;
use ReflectionException;

use org\schema\constants\Prop;

use function oihana\core\arrays\compress;

trait ThingTrait
{
    /**
     * Creates a new Thing instance.
     * @param array|object|null $init A generic object containing properties with which to populate the newly instance.
     * If this argument is null, it is ignored.
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
     * The @context attribute of the json-ld representation of the thing.
     */
    public const string CONTEXT = 'https://schema.org' ;

    /**
     * Invoked to serialize the object with the json serializer.
     * @throws ReflectionException
     */
    public function jsonSerialize() : array
    {
        $object =
        [
            Prop::AT_TYPE    => $this->getClassName( static::class ) ,
            Prop::AT_CONTEXT => static::CONTEXT
        ] ;

        $properties = $this->getPublicProperties( static::class ) ;
        foreach( $properties as $property )
        {
            $name = $property->getName();
            $object[ $name ] = $this->{ $name } ?? null ;
        }

        return compress( $object ) ;
    }
}