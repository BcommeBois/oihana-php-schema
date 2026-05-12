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
    /**
     * The audit event tag (e.g. `role.created`, `user.deleted`).
     */
    public const string EVENT = 'event' ;

    /**
     * The IP address of the client that performed the action.
     */
    public const string IP = 'ip' ;

    /**
     * The HTTP method used (POST, PATCH, PUT, DELETE).
     */
    public const string METHOD = 'method' ;

    /**
     * The machine-readable outcome of the action (e.g. `success`, `denied`, `error`).
     */
    public const string OUTCOME = 'outcome' ;

    /**
     * The full request path (e.g. `/roles/42`).
     */
    public const string PATH = 'path' ;

    /**
     * The resource collection name (e.g. `roles`, `users`, `permissions`).
     */
    public const string RESOURCE = 'resource' ;

    /**
     * The `_key` of the targeted document in the resource collection.
     */
    public const string RESOURCE_ID = 'resourceId' ;

    /**
     * The HTTP status code of the response.
     */
    public const string STATUS_CODE = 'statusCode' ;
}
