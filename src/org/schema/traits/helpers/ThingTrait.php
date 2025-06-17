<?php

namespace org\schema\traits\helpers ;

use ReflectionException;

use oihana\reflections\traits\ReflectionTrait;
use function oihana\core\arrays\compress;

use org\schema\constants\properties\Prop;

trait ThingTrait
{
    /**
     * Creates a new Thing instance.
     * @param array|object|null $init A generic object containing properties with which to populate the newly instance. If this argument is null, it is ignored.
     */
    public function __construct( array|object|null $init = null )
    {
        if( isset( $init ) )
        {
            if( is_array( $init ) )
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
    }

    use ReflectionTrait ;

    /**
     * The @context of the json-ld representation of the thing.
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
            Prop::AT_TYPE    => $this->getClassName( $this ) ,
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