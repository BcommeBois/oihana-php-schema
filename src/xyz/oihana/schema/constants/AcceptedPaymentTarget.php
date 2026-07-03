<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait;

/**
 * The enumeration of the 'accepted payment' flag to retrieves the specific customer or provider items.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants
 * @since   1.3.0
 */
class AcceptedPaymentTarget
{
    use ConstantsTrait ;

    public const int CUSTOMER = 1 ;
    public const int PROVIDER = 0 ;
}