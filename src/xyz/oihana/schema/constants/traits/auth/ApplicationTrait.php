<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all Application properties.
 *
 * Properties already available via other traits:
 * - ACTIVE, OWNER, ADDITIONAL_TYPE, IDENTIFIER (Schema.org Properties)
 * - CLIENT_ID (WebApplicationTrait)
 * - SCOPES, SCOPES_COUNT (ScopeTrait)
 * - PERMISSIONS, PERMISSIONS_COUNT (RoleTrait)
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 */
trait ApplicationTrait
{
    const string ALLOWED_IPS  = 'allowedIPs' ;
    const string DEFAULT      = 'default' ;
    const string EXPIRES_AT   = 'expiresAt' ;
    const string LAST_USED_AT = 'lastUsedAt' ;
    const string METADATA     = 'metadata' ;
}
