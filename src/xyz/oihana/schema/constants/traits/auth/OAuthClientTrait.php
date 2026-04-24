<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all OAuthClient properties.
 *
 * Properties already available via other traits:
 * - ACTIVE, NAME, DESCRIPTION, IDENTIFIER (Schema.org Properties)
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 */
trait OAuthClientTrait
{
    const string APP_ID    = 'appId' ;
    const string CLIENT_ID = 'clientId' ;
}
