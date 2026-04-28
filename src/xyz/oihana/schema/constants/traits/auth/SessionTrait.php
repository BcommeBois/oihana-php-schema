<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all Session properties.
 *
 * Properties already available via other traits:
 * - ACTIVE, IDENTIFIER, OWNER (Schema.org Properties)
 * - METADATA, EXPIRES_AT (ApplicationTrait)
 *
 * Note: This is a trait for use with Prop class. To reference constants
 * directly in controllers, use the Session schema class or Prop.
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 */
trait SessionTrait
{
    const string CLIENT_ID         = 'clientId' ;
    const string CURRENT           = 'current' ;
    const string EXPIRES_AT        = 'expiresAt' ;
    const string IP                = 'ip' ;
    const string REVOKED_AT        = 'revokedAt' ;
    const string REVOCATION_REASON = 'revocationReason' ;
    const string TOKEN_HASH        = 'tokenHash' ;
    const string USER_AGENT        = 'userAgent' ;
    const string USER_ID           = 'userId' ;
}
