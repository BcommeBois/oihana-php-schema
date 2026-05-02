<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of properties shared by protected/system resources
 * (Policy, Role, ...): visual color, write-protection flag and system flag.
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 */
trait ProtectedResourceTrait
{
    const string COLOR     = 'color'     ;
    const string PROTECTED = 'protected' ;
    const string SYSTEM    = 'system'    ;
}
