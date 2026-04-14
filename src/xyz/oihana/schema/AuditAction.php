<?php

namespace xyz\oihana\schema;

use org\schema\Action;

use xyz\oihana\schema\constants\Oihana;

/**
 * Represents an auditable action performed by an authenticated user.
 *
 * Extends the Schema.org Action type with audit-specific properties
 * for request tracking and RGPD-compliant logging.
 *
 * The `additionalType` property identifies the specific action type
 * using Schema.org URLs (e.g. https://schema.org/CreateAction).
 *
 * The `agent` property stores the ArangoDB _key of the user (not PII).
 * A join with the users collection resolves the user identity at display time.
 *
 * ### Example
 *
 * ```json
 * {
 *     "@type": "AuditAction",
 *     "@context": "https://schema.oihana.xyz",
 *     "additionalType": "https://schema.org/CreateAction",
 *     "agent": "72488862",
 *     "identifier": "364646423545321675",
 *     "description": "Created role",
 *     "target": "/roles",
 *     "instrument": "POST",
 *     "actionStatus": "CompletedActionStatus",
 *     "resource": "roles",
 *     "resourceId": "42",
 *     "statusCode": 201,
 *     "ip": "192.168.1.1",
 *     "method": "POST",
 *     "path": "/roles"
 * }
 * ```
 *
 * @package xyz\oihana\schema\auth
 * @author  Marc Alcaraz
 *
 * @see https://schema.org/Action
 */
class AuditAction extends Action
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The IP address of the client that performed the action.
     *
     * @var string|null
     */
    public string|null $ip ;

    /**
     * The HTTP method used (POST, PATCH, PUT, DELETE).
     *
     * @var string|null
     */
    public string|null $method ;

    /**
     * The full request path (e.g. /roles/42).
     *
     * @var string|null
     */
    public string|null $path ;

    /**
     * The resource collection name (e.g. roles, users, permissions).
     *
     * @var string|null
     */
    public string|null $resource ;

    /**
     * The _key of the targeted document in the resource collection.
     *
     * @var string|null
     */
    public string|null $resourceId ;

    /**
     * The HTTP status code of the response.
     *
     * @var int|null
     */
    public int|null $statusCode ;
}
