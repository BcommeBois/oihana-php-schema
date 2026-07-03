<?php

namespace xyz\oihana\schema\people;

use oihana\reflect\attributes\HydrateWith;

use org\schema\constants\Schema;
use org\schema\Organization;
use org\schema\Person as SchemaPerson;
use org\schema\PropertyValue;
use org\schema\traits\helpers\SetAdditionalPropertyTrait;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\PersonAdditionalProperty;
use xyz\oihana\schema\traits\SetContactPointTrait;

/**
 * A custom Person definition, Someone working for an organization.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\people
 * @since   1.3.0
 */
class Person extends SchemaPerson
{
    use SetAdditionalPropertyTrait ,
        SetContactPointTrait       ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * A property-value pair representing an additional characteristic of the entity,
     * e.g. a product feature or another characteristic for which there is no matching property in schema.org.
     */
    #[HydrateWith( PropertyValue::class ) ]
    public null|array|PropertyValue $additionalProperty = null ;

    /**
     * The owner organization of the person.
     * @var int|string|Organization|SchemaPerson|null
     */
    public null|int|string|Organization|SchemaPerson $ownedBy ;

    /**
     * The position of an item in a series or sequence of items.
     */
    public null|int|string $position ;

    /**
     * @param string $property Property name
     * @param mixed $value Value of the property.
     * @return void
     */
    public function __set( string $property , mixed $value ) :void
    {
        $this->setAdditionalProperties ( $property , $value ) ||
        $this->setContactPointProperty ( $property , $value ) ;
    }

    /**
     * Set a new optional additional properties of the customer.
     *
     * @param string $property Property name
     * @param mixed  $value    Value of the property.
     *
     * @return bool True if the property was handled, false otherwise
     */
    public function setAdditionalProperties( string $property , mixed $value ) :bool
    {
        if( PersonAdditionalProperty::includes( $property ) && isset( $value ) && is_string( $value ) )
        {
            $this->setAdditionalProperty
            ([
                Schema::PROPERTY_ID => $property ,
                Schema::VALUE       => PersonAdditionalProperty::normalize( $property , $value )
            ]) ;
            return true;
        }
        return false ;
    }
}