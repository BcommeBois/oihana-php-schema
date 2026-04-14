<?php

namespace xyz\oihana\schema\constants\traits;

/**
 * The enumeration of all AuditAction properties.
 *
 * @package xyz\oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 * @since   1.0.0
 */
trait AuditTrait
{
    public const string IP          = 'ip' ;
    public const string METHOD      = 'method' ;
    public const string PATH        = 'path' ;
    public const string RESOURCE    = 'resource' ;
    public const string RESOURCE_ID = 'resourceId' ;
    public const string STATUS_CODE = 'statusCode' ;
}
