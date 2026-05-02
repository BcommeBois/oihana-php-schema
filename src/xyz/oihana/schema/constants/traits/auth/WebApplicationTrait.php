<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all WebApplication properties.
 *
 * Properties already available via other traits:
 * - CLIENT_ID (ClientIdTrait)
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
trait WebApplicationTrait
{
    use ClientIdTrait ;

    const string API_IDENTIFIER            = 'apiIdentifier'          ;
    const string APPLICATION_TYPE          = 'applicationType'        ;
    const string POST_LOGOUT_REDIRECT_URIS = 'postLogoutRedirectUris' ;
    const string REDIRECT_URIS             = 'redirectUris'           ;
}