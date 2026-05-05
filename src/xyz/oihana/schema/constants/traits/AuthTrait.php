<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\auth\ApplicationTrait;
use xyz\oihana\schema\constants\traits\auth\InvitationTrait;
use xyz\oihana\schema\constants\traits\auth\KeyfileTrait;
use xyz\oihana\schema\constants\traits\auth\PermissionTrait;
use xyz\oihana\schema\constants\traits\auth\PolicyTrait;
use xyz\oihana\schema\constants\traits\auth\RoleTrait;
use xyz\oihana\schema\constants\traits\auth\SessionTrait;
use xyz\oihana\schema\constants\traits\auth\UserTrait;
use xyz\oihana\schema\constants\traits\auth\WebApplicationTrait;
use xyz\oihana\schema\constants\traits\auth\WebAPITrait;

trait AuthTrait
{
    use ApplicationTrait ,
        KeyfileTrait ,
        InvitationTrait ,
        PermissionTrait ,
        PolicyTrait ,
        RoleTrait ,
        SessionTrait ,
        UserTrait ,
        WebApplicationTrait ,
        WebAPITrait ;
}