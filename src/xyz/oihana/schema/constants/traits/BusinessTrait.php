<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\business\BusinessIdentityTrait;
use xyz\oihana\schema\constants\traits\business\UserProfileTrait;

/**
 * The enumeration of all business properties.
 *
 * @package xyz\oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
trait BusinessTrait
{
    use BusinessIdentityTrait ,
         UserProfileTrait     ;
}