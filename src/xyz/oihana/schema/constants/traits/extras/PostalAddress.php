<?php

namespace xyz\oihana\schema\constants\traits\extras ;

/**
 * The enumeration of all extra postal address properties.
 *
 * @package xyz\oihana\schema\constants\traits\extras
 * @author  Marc Alcaraz
 * @since   1.3.0
 */
trait PostalAddress
{
    public const string ADDRESS_ALTERNATE_NAME   = 'addressAlternateName' ;
    public const string ADDRESS_AREA_SERVED      = 'addressAreaServed' ;
    public const string ADDRESS_EMAIL            = 'addressEmail' ;
    public const string ADDRESS_EXTENDED_ADDRESS = 'addressExtendedAddress' ;
    public const string ADDRESS_FAX_NUMBER       = 'addressFaxNumber' ;
    public const string ADDRESS_NAME             = 'addressName' ;
    public const string ADDRESS_TELEPHONE        = 'addressTelephone' ;

    public const string ADDRESS_DOT_AREA_SERVED = 'address.areaServed' ;
}