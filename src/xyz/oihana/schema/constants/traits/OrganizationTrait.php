<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\organizations\Company;
use xyz\oihana\schema\constants\traits\organizations\Customer;
use xyz\oihana\schema\constants\traits\organizations\Provider;

/**
 * The enumeration of all organizations properties constants.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz
 * @since   1.3.0
 */
trait OrganizationTrait
{
    use Company  ,
        Customer ,
        Provider ;
}