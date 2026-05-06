<?php

namespace xyz\oihana\schema\constants\auth ;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Form field names sent in the `application/x-www-form-urlencoded`
 * body of a request to the OAuth 2.0 token endpoint.
 *
 * Covers RFC 6749 §4.4 (`client_credentials` grant) and RFC 7521
 * (`client_assertion` extension for asymmetric client authentication).
 *
 * @package oihana\schema\constants\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
class TokenRequestField
{
    use ConstantsTrait ;

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
     * `client_assertion` — the signed JWT (RS256) carrying the
     * application's identity claims, signed with the keyfile's
     * private key.
     */
    public const string CLIENT_ASSERTION = 'client_assertion' ;

    /**
     * `client_assertion_type` — must be the constant
     * `urn:ietf:params:oauth:client-assertion-type:jwt-bearer`
     * (see {@see TokenRequestValue::JWT_BEARER_ASSERTION_TYPE}).
     */
    public const string CLIENT_ASSERTION_TYPE = 'client_assertion_type' ;

    /**
     * `grant_type` — must be `client_credentials` for the M2M flow
     * (see {@see TokenRequestValue::GRANT_CLIENT_CREDENTIALS}).
     */
    public const string GRANT_TYPE = 'grant_type' ;

    /**
     * `scope` — the requested OIDC / OAuth scope. `openid` is the
     * default ; some IdPs require an additional project-scoped
     * audience (e.g. `urn:zitadel:iam:org:project:id:<id>:aud`).
     */
    public const string SCOPE = 'scope' ;
}
