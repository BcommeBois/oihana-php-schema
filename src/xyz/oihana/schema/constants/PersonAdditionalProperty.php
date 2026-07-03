<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait;

use xyz\oihana\schema\constants\traits\people\Employee;

/**
 * Enumeration of all additional properties related to a Person.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants
 * @since   1.3.0
 */
class PersonAdditionalProperty
{
    use ConstantsTrait ,
        Employee       ;

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
            self::IS_DOCUMENT_RECIPIENT      ,
            self::IS_QUOTE_RECIPIENT         ,
            self::IS_DELIVERY_NOTE_RECIPIENT ,
            self::IS_ORDER_RECIPIENT         ,
            self::IS_INVOICE_RECIPIENT       ,
            self::SHOW_APPLICATIONS          => (bool) $value ,
            default                          => $value ,
        };
    }
}