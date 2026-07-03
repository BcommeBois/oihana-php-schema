<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait ;
use xyz\oihana\schema\constants\traits\places\Site ;

/**
 * Enumeration of all additional properties related to a Site.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants
 * @since   1.3.0
 */
class SiteAdditionalProperty
{
    use ConstantsTrait ,
        Site           ;

    /**
     * Normalize the value of the specific property.
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    public static function normalize( string $property , mixed $value ) :mixed
    {
        return match( $property )
        {
            self::IS_BILLING_ADDRESS   ,
            self::IS_CONSTRUCTION_SITE ,
            self::IS_DEFAULT_ADDRESS   ,
            self::IS_DELIVERY_ADDRESS  ,
            self::IS_SHIPPING_ADDRESS => (bool) $value ,
            default                   => $value ,
        };
    }
}