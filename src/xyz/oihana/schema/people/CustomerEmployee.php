<?php

namespace xyz\oihana\schema\people ;

use xyz\oihana\schema\traits\people\EmployeeFlagsTrait;

/**
 * Someone working for a customer organization.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\people
 * @since   1.3.0
 */
class CustomerEmployee extends Person
{
    use EmployeeFlagsTrait ;
}