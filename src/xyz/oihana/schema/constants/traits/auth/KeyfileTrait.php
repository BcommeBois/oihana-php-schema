<?php

namespace xyz\oihana\schema\constants\traits\auth ;

/**
 * The enumeration of all keyfile properties.
 *
 * Mirrors the JSON keyfile structure returned by Zitadel's
 * `CreateApplicationKey` endpoint when an API application is
 * configured with `authMethodType = API_AUTH_METHOD_TYPE_PRIVATE_KEY_JWT`.
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
trait KeyfileTrait
{
    const string APP_ID    = 'appId'    ;
    const string CLIENT_ID = 'clientId' ;
    const string KEY       = 'key'      ;
    const string KEY_ID    = 'keyId'    ;
    const string TYPE      = 'type'     ;
}