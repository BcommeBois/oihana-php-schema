<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait;
use xyz\oihana\schema\constants\traits\organizations\Customer;

/**
 * Enumeration of all additional properties related to a Customer.
 *
 * Each constant represents a specific configurable feature or flag for a customer,
 * typically used in order processing, receipt generation, and picking list preparation.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants
 * @since   1.3.0
 */
class CustomerAdditionalProperty
{
    use ConstantsTrait ,
        Customer       ;

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
            self::GENERATE_ACKNOWLEDGING_RECEIPT ,
            self::ORDER_SHOW_IDENTIFIER          ,
            self::PRINT_AND_MAIL_INVOICE         ,
            self::SHOW_APPLICATIONS              => (bool) $value ,
            self::INVOICE_ISSUE_INTERVAL         => $value !== null ? (int) $value : null ,
            default                              => $value ,
        };
    }
}