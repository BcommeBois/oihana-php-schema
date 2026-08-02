<?php

namespace xyz\oihana\schema\places;

use org\schema\constants\Schema;
use org\schema\traits\helpers\SetAdditionalPropertyTrait;

use ReflectionException;
use xyz\oihana\schema\constants\SiteAdditionalProperty;
use xyz\oihana\schema\traits\SetContactPointTrait;
use xyz\oihana\schema\traits\SetGeoCoordinatesTrait;
use xyz\oihana\schema\traits\SetPostalAddressTrait;

/**
 * The house Place representation : a location described by a flat dataset row.
 *
 * Where {@see \org\schema\Place} mirrors Schema.org, this one adds the ingestion
 * behavior the back office needs — the same set {@see \xyz\oihana\schema\traits\SiteTrait}
 * groups for the site flavours, composed here directly. A flat row can therefore
 * be assigned property by property, without the caller mapping anything by hand :
 * see {@see Place::__set()} for what is routed where.
 *
 * Note the hierarchy, which reads the other way round from what the names suggest :
 * this class extends {@see Site}, which extends the Schema.org `Place`. `Site` is
 * where the shared members live — `contactPoint`, `deliveryMethod`, `ownedBy`,
 * `position` — so `Place` sits alongside `Office`, `Warehouse`, `JobSite`,
 * `CustomerSite` and `ProviderSite` rather than above them.
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
     * Routes an unknown property expression to the handler that knows it.
     *
     * The handlers are tried in order and the first one to claim the expression
     * wins : {@see Place::setAdditionalProperties()} for the flags and extras
     * listed by {@see SiteAdditionalProperty}, then
     * {@see SetGeoCoordinatesTrait::setGeoCoordinatesProperties()} for
     * `geoLatitude`, `geoLongitude`, `geoDistance` and `geoElevation`, then
     * {@see SetPostalAddressTrait::setPostalAddressProperties()} for the address
     * fields. An expression no handler claims is silently dropped — this hook
     * only ever fires for properties the class does not declare, so there is
     * nowhere to put it.
     *
     * Contact points are the deliberate gap : {@see SetContactPointTrait} is
     * composed, but `setContactPointProperty()` is not part of this chain, so
     * `mobile`, `default_email` and their siblings are not ingested here. They
     * are on {@see \xyz\oihana\schema\organizations\Company} and
     * {@see \xyz\oihana\schema\people\Person}, which do call it.
     *
     * @param string $property Property name.
     * @param mixed  $value    Value of the property.
     *
     * @return void
     *
     * @throws ReflectionException
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
     *
     * @throws ReflectionException
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