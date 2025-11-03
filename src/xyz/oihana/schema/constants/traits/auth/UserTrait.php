<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all Permission properties.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
trait UserTrait
{
    const string APP_META_DATA     = 'appMetadata'  ;
    const string APPLICATIONS      = 'applications'  ;
    const string BLOCKED_FOR       = 'blockedFor' ;
    const string DEVICES           = 'devices' ;
    const string LAST_LOGIN        = 'lastLogin' ;
    const string LOGINS_COUNT      = 'loginsCount' ;
    const string METADATA          = 'metadata' ;
    const string PERMISSIONS       = 'permissions' ;
    const string PERMISSIONS_COUNT = 'permissionsCount' ;
    const string ROLES             = 'roles' ;
    const string ROLES_COUNT       = 'rolesCount' ;
    const string SIGNED_UP         = 'signedUp' ;
}