<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all Roles properties.
 *
 * Properties already available via other traits:
 * - PERMISSIONS, PERMISSIONS_COUNT (PermissionsTrait)
 * - POLICIES, POLICIES_COUNT       (PoliciesTrait)
 * - USERS, USERS_COUNT             (UsersTrait)
 * - COLOR, PROTECTED, SYSTEM       (ProtectedResourceTrait)
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
trait RoleTrait
{
    use PermissionsTrait       ,
        PoliciesTrait          ,
        ProtectedResourceTrait ,
        UsersTrait             ;

    const string DEFAULT = 'default' ;
    const string LEVEL   = 'level'   ;
}
