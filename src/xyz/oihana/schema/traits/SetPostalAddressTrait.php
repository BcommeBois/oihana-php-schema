<?php

namespace xyz\oihana\schema\traits;

use org\schema\constants\Schema;
use org\schema\PostalAddress;

use xyz\oihana\schema\constants\Oihana;

use function oihana\core\accessors\setKeyValue;

/**
 * Injects and normalizes the postal address of an entity from flat property expressions.
 *
 * The consumer class must declare an `address` property. Flat keys, e.g. `addressEmail`
 * or `streetAddress`, are routed to the nested {@see \org\schema\PostalAddress}
 * instance, created on demand.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\traits
 * @since   1.3.0
 */
trait SetPostalAddressTrait
{
    /**
     * Normalize a PostalAddress definition and search if the streetAddress expression contains a ';' separator
     * to extract properties from the specific field.
     *
     * @param mixed $definition The definition to normalize.
     * @param string $separator The separator to explode the postal address expression (default ';')
     * @return mixed
     */
    public static function normalizePostalAddress( mixed $definition , string $separator = ';' ):mixed
    {
        if( $definition instanceof PostalAddress )
        {
            $streetAddress = $definition->streetAddress ?? null ;
            if( !empty( $streetAddress ) && str_contains( $streetAddress, $separator ) )
            {
                [ $streetAddress , $extendedAddress , $postOfficeBoxNumber ] = explode( $separator , $streetAddress ) + [ null , null , null ] ;

                $definition->streetAddress       = $streetAddress       ?: null ;
                $definition->extendedAddress     = $extendedAddress     ?: null ;
                $definition->postOfficeBoxNumber = $postOfficeBoxNumber ?: null ;
            }
        }
        return $definition ;
    }

    /**
     * Internal method to inject custom properties in the PostalAddress property of the place.
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function setPostalAddressProperty( string $name , mixed $value ):bool
    {
        if ( empty( $value ) )
        {
            if ( $this->address instanceof PostalAddress )
            {
                setKeyValue( $this->address , $name , null ) ;
                return true ;
            }
            return false ;
        }

        if ( $name === Schema::EMAIL && !filter_var( $value , FILTER_VALIDATE_EMAIL ) )
        {
            $value = null ;
            // return false ;
        }

        if ( !$this->address instanceof PostalAddress )
        {
            $this->address = new PostalAddress() ;
        }

        setKeyValue( $this->address , $name , $value ) ;

        return true ;
    }

    /**
     * Set the PostalAddress properties.
     * @param string $property
     * @param mixed $value
     * @return bool
     */
    public function setPostalAddressProperties( string $property , mixed $value ) :bool
    {
        return match( $property )
        {
            // ------- custom property redirect

            Oihana::ADDRESS_AREA_SERVED    => $this->setPostalAddressProperty(Schema::AREA_SERVED    , $value ) ,
            Oihana::ADDRESS_EMAIL          => $this->setPostalAddressProperty(Schema::EMAIL          , $value ) ,
            Oihana::ADDRESS_FAX_NUMBER     => $this->setPostalAddressProperty(Schema::FAX_NUMBER     , $value ) ,
            Oihana::ADDRESS_ALTERNATE_NAME => $this->setPostalAddressProperty(Schema::ALTERNATE_NAME , $value ) ,
            Oihana::ADDRESS_NAME           => $this->setPostalAddressProperty(Schema::NAME           , $value ) ,
            Oihana::ADDRESS_TELEPHONE      => $this->setPostalAddressProperty(Schema::TELEPHONE      , $value ) ,

            // ------- direct property

            Schema::STREET_ADDRESS          ,
            Schema::ADDRESS_COUNTRY         ,
            Schema::ADDRESS_LOCALITY        ,
            Schema::EXTENDED_ADDRESS        ,
            Schema::POSTAL_CODE             ,
            Schema::POST_OFFICE_BOX_NUMBER  => $this->setPostalAddressProperty( $property , $value ),

            default                      => false,
        };
    }
}