<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all Session properties.
 *
 * Properties already available via other traits:
 * - ACTIVE, IDENTIFIER, OWNER (Schema.org Properties)
 * - CLIENT_ID                 (ClientIdTrait)
 * - METADATA, EXPIRES_AT      (ApplicationTrait)
 *
 * Note: This is a trait for use with Prop class. To reference constants
 * directly in controllers, use the Session schema class or Prop.
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 */
trait SessionTrait
{
    use ClientIdTrait ;

    const string CURRENT           = 'current'          ;
    const string EXPIRES_AT        = 'expiresAt'        ;
    const string IP                = 'ip'               ;
    const string METADATA          = 'metadata'         ;
    const string REVOKED_AT        = 'revokedAt'        ;
    const string REVOCATION_REASON = 'revocationReason' ;
    const string TOKEN_HASH        = 'tokenHash'        ;
    const string USER_AGENT        = 'userAgent'        ;
    const string USER_ID           = 'userId'           ;
}
