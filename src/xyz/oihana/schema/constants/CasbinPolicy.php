<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Defines the Casbin policy keys.

 * @package xyz\oihana\schema
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class CasbinPolicy
{
    use ConstantsTrait ;

    /**
     * The 'act' policy key.
     */
    public const string ACTION = 'act' ;

    /**
     * The 'dom' policy key.
     */
    public const string DOMAIN = 'dom' ;

    /**
     * The 'eft' policy key.
     */
    public const string EFFECT = 'eft' ;

    /**
     * The 'obj' policy key.
     */
    public const string OBJECT = 'obj' ;

    /**
     * The 'sub' policy key.
     */
    public const string SUBJECT = 'sub' ;

}