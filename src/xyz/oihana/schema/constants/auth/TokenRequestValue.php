<?php

namespace xyz\oihana\schema\constants\auth ;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Constant values for the OAuth 2.0 token request fields.
 *
 * Pairs with {@see TokenRequestField} : the field carries the param
 * name, this class carries the (often lengthy URI-shaped) value.
 *
 * @package oihana\schema\constants\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
class TokenRequestValue
{
    use ConstantsTrait ;

    /**
     * Default OIDC scope requested at the token endpoint. Most
     * IdPs accept `openid` alone for `client_credentials` ; some
     * setups require a project-aware audience scope to obtain a
     * JWT carrying the right `aud` claim.
     */
    public const string DEFAULT_SCOPE = 'openid' ;

    /**
     * RFC 6749 §4.4 grant_type for the M2M client_credentials flow.
     */
    public const string GRANT_CLIENT_CREDENTIALS = 'client_credentials' ;

    /**
     * RFC 7523 grant_type for the JWT-bearer authorization grant.
     *
     * Distinct from the `client_credentials` grant + `client_assertion`
     * pair, which authenticates an OAuth-registered client. The
     * `jwt-bearer` grant treats the JWT itself as the identity proof —
     * the canonical pattern for Service Users authenticating with a
     * private key on Zitadel.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc7523 RFC 7523
     */
    public const string GRANT_JWT_BEARER = 'urn:ietf:params:oauth:grant-type:jwt-bearer' ;

    /**
     * RFC 7521 client_assertion_type identifying a JWT bearer
     * assertion as the client authentication mechanism.
     */
    public const string JWT_BEARER_ASSERTION_TYPE = 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer' ;
}
