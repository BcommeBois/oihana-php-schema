<?php

namespace xyz\oihana\schema\constants\traits\extras;

/**
 * The enumeration of all extra contact point type properties.
 *
 * @package xyz\oihana\schema\constants\traits\extras
 * @author  Marc Alcaraz
 * @since   1.3.0
 */
trait ContactPointType
{
    public const string DEFAULT_EMAIL       = 'default_email'     ;
    public const string DEFAULT_FAX_NUMBER  = 'default_faxNumber' ;
    public const string DEFAULT_TELEPHONE   = 'default_telephone' ;

    public const string HOME_EMAIL          = 'home_email'     ;
    public const string HOME_FAX_NUMBER     = 'home_faxNumber' ;
    public const string HOME_TELEPHONE      = 'home_telephone' ;

    public const string LANDLINE_EMAIL      = 'landline_email'     ;
    public const string LANDLINE_FAX_NUMBER = 'landline_faxNumber' ;
    public const string LANDLINE_TELEPHONE  = 'landline_telephone' ;

    public const string MOBILE              = 'mobile' ;
    public const string MOBILE_PROFESSIONAL = 'mobile_professional' ;
}