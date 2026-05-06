<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all Application properties.
 *
 * Properties already available via other traits:
 * - ACTIVE, OWNER, ADDITIONAL_TYPE, IDENTIFIER (Schema.org Properties)
 * - CLIENT_ID (ClientIdTrait)
 * - PERMISSIONS, PERMISSIONS_COUNT (PermissionsTrait)
 * - POLICIES, POLICIES_COUNT (PoliciesTrait)
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 */
trait ServiceTrait
{
    use ClientIdTrait    ,
        PermissionsTrait ,
        PoliciesTrait    ;

    const string ALLOWED_IPS     = 'allowedIPs'     ;
    const string CREATED_BY      = 'createdBy'      ;
    const string DISABLED_AT     = 'disabledAt'     ;
    const string DISABLED_BY     = 'disabledBy'     ;
    const string DISABLED_REASON = 'disabledReason' ;
    const string EXPIRES_AT      = 'expiresAt'      ;
    const string KEY_ID          = 'keyId'          ;
    const string KEYFILE         = 'keyfile'        ;
    const string LAST_SEEN_IP    = 'lastSeenIP'     ;
    const string LAST_USED_AT    = 'lastUsedAt'     ;
    const string METADATA        = 'metadata'       ;
    const string PROTECTED       = 'protected'      ;
}
