<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Defines supported Permission effects.

 * @package xyz\oihana\schema
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class Effect
{
    use ConstantsTrait ;

    /**
     * The 'allow' effect of a specific permission.
     */
    public const string ALLOW = 'allow' ;

    /**
     * The 'deny' effect of a specific permission.
     */
    public const string DENY  = 'deny' ;

}