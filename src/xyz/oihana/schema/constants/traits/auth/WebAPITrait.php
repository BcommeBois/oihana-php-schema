<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all WebAPI properties.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
trait WebAPITrait
{
    const string ALGORITHM                       = 'algorithm' ;
    const string ALLOW_OFFLINE_ACCESS            = 'allowOfflineAccess' ;
    const string ALLOW_SKIP_USER_CONSENT         = 'allowSkipUserConsent' ;
    const string IMPLICIT_HYBRID_TOKEN_LIFETIME  = 'implicitHybridTokenLifetime' ;
    const string MAXIMUM_ACCESS_TOKEN_EXPIRATION = 'maximumAccessTokenExpiration' ;
    const string PERMISSIONS                     = 'permissions' ;
    const string PERMISSIONS_COUNT               = 'permissionsCount' ;
    const string RBAC                            = 'rbac' ;
    const string SCOPE_HAS_PERMISSION            = 'scopeHasPermission' ;
}