<?php

namespace xyz\oihana\schema\constants\traits;

/**
 * The enumeration of all AuthWebAPI properties.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
trait AuthWebAPITrait
{
    const string ALGORITHM                       = 'algorithm' ;
    const string ALLOW_OFFLINE_ACCESS            = 'allowOfflineAccess' ;
    const string IMPLICIT_HYBRID_TOKEN_LIFETIME  = 'implicitHybridTokenLifetime' ;
    const string MAXIMUM_ACCESS_TOKEN_EXPIRATION = 'maximumAccessTokenExpiration' ;
    const string PERMISSIONS                     = 'permissions' ;
    const string PERMISSIONS_COUNT               = 'permissionsCount' ;
    const string RBAC                            = 'rbac' ;
    const string SCOPE_HAS_PERMISSION            = 'scopeHasPermission' ;
    const string SKIP_USER_CONSENT               = 'skipUserConsent' ;
}