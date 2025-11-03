<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\auth\PermissionTrait;
use xyz\oihana\schema\constants\traits\auth\RoleTrait;
use xyz\oihana\schema\constants\traits\auth\UserTrait;
use xyz\oihana\schema\constants\traits\auth\WebAPITrait;

trait AuthTrait
{
    use PermissionTrait ,
        RoleTrait ,
        UserTrait ,
        WebAPITrait ;
}