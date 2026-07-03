<?php

namespace xyz\oihana\schema\places;

use org\schema\constants\Schema;
use org\schema\ContactPoint;
use org\schema\traits\helpers\SetAdditionalPropertyTrait;

use xyz\oihana\schema\constants\SiteAdditionalProperty;
use xyz\oihana\schema\traits\SetContactPointTrait;
use xyz\oihana\schema\traits\SetGeoCoordinatesTrait;
use xyz\oihana\schema\traits\SetPostalAddressTrait;

/**
 * The custom Place representation.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\places
 * @since   1.3.0
 */
class Place extends Site
{
    use SetAdditionalPropertyTrait ,
        SetContactPointTrait       ,
        SetGeoCoordinatesTrait     ,
        SetPostalAddressTrait      ;

    /**
     * A contact point for a person or organization.
     * @var null|ContactPoint|array|string
     */
    public null|ContactPoint|array|string $contactPoint ;

    /**
     * @param string $property Property name.
     * @param mixed  $value    Value of the property.
     * @return void
     */
    public function __set( string $property , mixed $value ) :void
    {
        $this->setAdditionalProperties     ( $property , $value ) ||
        $this->setGeoCoordinatesProperties ( $property , $value ) ||
        $this->setPostalAddressProperties  ( $property , $value ) ;
    }

    /**
     * Set a new optional additional properties of the place.
     *
     * @param string $property Property name.
     * @param mixed  $value    Value of the property.
     *
     * @return bool True if the property was handled, false otherwise
     */
    public function setAdditionalProperties( string $property , mixed $value ) :bool
    {
        if( SiteAdditionalProperty::includes( $property ) && isset( $value ) && is_string( $value ) )
        {
            $this->setAdditionalProperty
            ([
                Schema::PROPERTY_ID => $property ,
                Schema::VALUE       => SiteAdditionalProperty::normalize( $property , $value )
            ]) ;
            return true;
        }
        return false ;
    }
}