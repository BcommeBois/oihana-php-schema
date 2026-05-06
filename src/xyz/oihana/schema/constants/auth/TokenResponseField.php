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
     * `assertion` — a JWT or token assertion returned by certain
     * authorization servers (or used in extension flows such as
     * JWT Bearer Token Grant, RFC 7523).
     *
     * This field may contain the original client assertion or a
     * derived token used for downstream validation, delegation,
     * or token exchange scenarios. It is not part of the core
     * RFC 6749 §5.1 response but may appear in extended or
     * vendor-specific implementations.
     */
    public const string ASSERTION = 'assertion' ;

    /**
     * `expires_at` — absolute expiration timestamp (epoch seconds)
     * of the access_token.
     *
     * Non-standard but sometimes provided by certain providers as an
     * alternative to `expires_in`.
     */
    public const string EXPIRES_AT = 'expires_at' ;

    /**
     * `expires_in` — number of seconds the access_token remains
     * valid, counted from the response time. Used to seed the local
     * cache TTL (refreshed proactively `REFRESH_SAFETY_MARGIN`
     * seconds before that hard expiration to absorb clock drift).
     */
    public const string EXPIRES_IN = 'expires_in' ;

    /**
     * `id_token` — a JWT containing identity claims about the client
     * or subject, returned in OpenID Connect flows.
     *
     * Not part of pure OAuth2, but commonly included when OIDC is
     * enabled on the token endpoint.
     */
    public const string ID_TOKEN = 'id_token' ;

    /**
     * `refresh_token` — a long-lived token that can be used to obtain
     * a new access_token without re-authenticating the client.
     *
     * Rare in pure client_credentials flows, but may appear depending
     * on the authorization server configuration.
     */
    public const string REFRESH_TOKEN = 'refresh_token' ;

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
