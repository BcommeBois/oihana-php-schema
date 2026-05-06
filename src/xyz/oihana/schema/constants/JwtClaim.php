<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Standard JWT claim names used when building the bearer assertion
 * sent at the identity provider's token endpoint.
 *
 * Mirrors RFC 7519 §4.1 (Registered Claim Names). Only the claims
 * used by the M2M `client_credentials` + `jwt-bearer` flow are
 * included here ; project-specific or extended claims live in their
 * own dedicated enums.
 *
 * @package oihana\m2m\enums
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
class JwtClaim
{
    use ConstantsTrait;

    /**
     * `aud` — the JWT audience (typically the issuer URL of the IdP
     * for a `client_credentials` exchange).
     */
    public const string AUDIENCE = 'aud' ;

    /**
     * `exp` — the Unix epoch time at which the assertion stops being
     * valid. Must be in the near future (`now + 60s` is plenty).
     */
    public const string EXPIRES_AT = 'exp' ;

    /**
     * `iat` — the Unix epoch time at which the assertion was minted.
     */
    public const string ISSUED_AT = 'iat' ;

    /**
     * `iss` — the JWT issuer. For a `jwt-bearer` client assertion,
     * this is the OAuth client_id of the application.
     */
    public const string ISSUER = 'iss' ;

    /**
     * `sub` — the subject of the assertion. For a `jwt-bearer`
     * client assertion, this is the OAuth client_id of the
     * application (same value as {@see self::ISSUER}).
     */
    public const string SUBJECT = 'sub' ;

    /**
     * `jti` — unique identifier for the JWT (JWT ID).
     *
     * Used to prevent replay attacks by ensuring each assertion
     * is only used once. The authorization server may store and
     * reject already seen identifiers.
     */
    public const string JWT_ID = 'jti' ;

    /**
     * `nbf` — "not before" timestamp.
     *
     * Defines the earliest time at which the JWT becomes valid.
     * Helps mitigate clock skew between client and server.
     */
    public const string NOT_BEFORE = 'nbf' ;
}