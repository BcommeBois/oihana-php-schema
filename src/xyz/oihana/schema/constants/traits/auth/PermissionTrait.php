<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all Permission properties.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
trait PermissionTrait
{
    use ProtectedResourceTrait ;

    const string ACTION      = 'action'      ;
    const string DESCRIPTION = 'description' ;
    const string DOMAIN      = 'domain'      ;
    const string EFFECT      = 'effect'      ;
    const string NAME        = 'name'        ;
    const string OBJECT      = 'object'      ;
    const string SUBJECT     = 'subject'     ;
}