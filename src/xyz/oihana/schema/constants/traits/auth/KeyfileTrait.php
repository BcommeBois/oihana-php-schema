<?php

namespace xyz\oihana\schema\constants\traits\auth ;

/**
 * The enumeration of all keyfile properties.
 *
 * Combines the fields emitted natively by the IdP (TYPE, KEY_ID,
 * USER_ID, CLIENT_ID, KEY) with the connection metadata injected
 * by the API at service creation (ISSUER, AUDIENCE, SCOPE,
 * API_BASE_URL) so the resulting keyfile is self-sufficient for
 * an M2M client.
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
trait KeyfileTrait
{
    const string API_BASE_URL = 'apiBaseUrl' ;
    const string AUDIENCE     = 'audience'   ;
    const string CLIENT_ID    = 'clientId'   ;
    const string ISSUER       = 'issuer'     ;
    const string KEY          = 'key'        ;
    const string KEY_ID       = 'keyId'      ;
    const string SCOPE        = 'scope'      ;
    const string TYPE         = 'type'       ;
    const string USER_ID      = 'userId'     ;
}