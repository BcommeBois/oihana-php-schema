<?php

namespace xyz\oihana\schema\traits;

use org\schema\constants\Schema;
use org\schema\GeoCoordinates;

use xyz\oihana\schema\constants\Oihana;
use function oihana\core\accessors\setKeyValue;

/**
 * Injects the geo coordinates of a place from flat property expressions.
 *
 * Each flat key — `geoLatitude`, `geoLongitude`, `geoDistance`, `geoElevation` —
 * is written into the `geo` property of the consumer class, creating a
 * {@see GeoCoordinates} on first use. A non-numeric value clears the matching
 * field instead.
 *
 * The consumer class must therefore declare `geo` ; every place in this library
 * inherits it from {@see \org\schema\traits\PlaceTrait}.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\traits
 * @since   1.3.0
 */
trait SetGeoCoordinatesTrait
{
    /**
     * Internal method to inject custom properties in the PostalAddress property of the place.
     *
     * @param string $name The name of the property to transform.
     * @param mixed $value The value of the property.
     *
     * @return bool
     */
    public function setGeoCoordinatesProperty( string $name , mixed $value ):bool
    {
        if ( is_numeric( $value ) )
        {
            if ( ! isset( $this->geo ) )
            {
                $this->geo = new GeoCoordinates() ;
            }

            setKeyValue( $this->geo , $name , (float) $value ) ;

            return true ;
        }

        if ( isset( $this->geo ) && $this->geo instanceof GeoCoordinates )
        {
            setKeyValue( $this->geo , $name , null ) ;
            return true ;
        }

        return false ;
    }

    /**
     * Set the GeoCoordinates properties.
     *
     * @param string $property The property to inject.
     * @param mixed  $value    The value of the property.
     *
     * @return bool
     */
    public function setGeoCoordinatesProperties(string $property, mixed $value ): bool
    {
        return match( $property )
        {
            Oihana::GEO_DISTANCE  => $this->setGeoCoordinatesProperty( Schema::DISTANCE  , $value ) ,
            Oihana::GEO_ELEVATION => $this->setGeoCoordinatesProperty( Schema::ELEVATION , $value ) ,
            Oihana::GEO_LATITUDE  => $this->setGeoCoordinatesProperty( Schema::LATITUDE  , $value ) ,
            Oihana::GEO_LONGITUDE => $this->setGeoCoordinatesProperty( Schema::LONGITUDE , $value ) ,
            default               => false,
        };
    }
}