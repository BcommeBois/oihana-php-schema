<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all Policy properties.
 *
 * Properties already available via other traits:
 * - APPLICATIONS, APPLICATIONS_COUNT (ApplicationsTrait)
 * - PERMISSIONS, PERMISSIONS_COUNT   (PermissionsTrait)
 * - ROLES, ROLES_COUNT               (RolesTrait)
 * - COLOR, PROTECTED, SYSTEM         (ProtectedResourceTrait)
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 */
trait PolicyTrait
{
    use ApplicationsTrait      ,
        PermissionsTrait       ,
        ProtectedResourceTrait ,
        RolesTrait             ,
        ServicesTrait          ;
}
