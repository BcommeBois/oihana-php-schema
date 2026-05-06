<?php

namespace xyz\oihana\schema\constants\auth ;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Field names of the JSON body returned by the OAuth 2.0 token
 * endpoint after a successful `client_credentials` exchange.
 *
 * RFC 6749 §5.1 (Successful Response). Only the fields used by the
 * M2M client are surfaced here.
 *
 * @package oihana\schema\constants\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
class TokenResponseField
{
    use ConstantsTrait ;

    /**
     * `access_token` — the JWT to send back to the resource API as
     * `Authorization: Bearer <access_token>`.
     */
    public const string ACCESS_TOKEN = 'access_token' ;

    /**
     * `expires_in` — number of seconds the access_token remains
     * valid, counted from the response time. Used to seed the local
     * cache TTL (refreshed proactively `REFRESH_SAFETY_MARGIN`
     * seconds before that hard expiration to absorb clock drift).
     */
    public const string EXPIRES_IN = 'expires_in' ;

    /**
     * `scope` — the actual scopes granted (may differ from the
     * requested scope set if the IdP narrows them down).
     */
    public const string SCOPE = 'scope' ;

    /**
     * `token_type` — typically `Bearer`. Surfaced for completeness ;
     * the M2M client always uses `Bearer` regardless of this value.
     */
    public const string TOKEN_TYPE = 'token_type' ;
}
