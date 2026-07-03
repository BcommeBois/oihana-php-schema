<?php

namespace xyz\oihana\schema\traits ;

use org\schema\constants\Schema;
use org\schema\traits\helpers\SetAdditionalPropertyTrait;

use xyz\oihana\schema\constants\SiteAdditionalProperty;

/**
 * Groups the ingestion behaviors shared by all the site flavored places :
 * additional properties, geo coordinates, postal address and contact points.
 *
 * The `__set` hook routes any unknown property expression to the matching
 * handler, so a flat dataset row can hydrate a site without manual mapping.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\traits
 * @since   1.3.0
 */
trait SiteTrait
{
    use SetAdditionalPropertyTrait ,
        SetGeoCoordinatesTrait     ,
        SetPostalAddressTrait      ,
        SetContactPointTrait       ;

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