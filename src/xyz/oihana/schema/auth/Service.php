<?php

namespace xyz\oihana\schema\auth;

use oihana\reflect\attributes\HydrateWith;

use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\auth\ServiceTrait;

/**
 * Represents a Service Account (machine identity) backed by a Zitadel Machine User.
 * Holds OAuth2 client credentials issued via a Zitadel User Key (JWT private_key_jwt grant, RFC 7523)
 * and the audit fields tracking M2M activity.
 *
 * @see Keyfile
 * @see Permission
 * @see Policy
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
class Service extends Thing
{
    use ServiceTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * IP whitelist using glob patterns (fnmatch).
     * @var array|null
     */
    public array|null $allowedIPs ;

    /**
     * OAuth2 `client_id` exposed to the service for token requests.
     * With Zitadel Machine Users, equals `keyId` of the active User Key — surfaced as a separate field for symmetry with Application and to allow rotation.
     * @var string|null
     */
    public string|null $clientId ;

    /**
     * The user (or system) who created this service.
     * @var string|Thing|null
     */
    public string|null|Thing $createdBy ;

    /**
     * The date this service was disabled (ISO 8601).
     * @var string|null
     */
    public string|null $disabledAt ;

    /**
     * The user (or system) who disabled this service.
     * @var string|Thing|null
     */
    public string|null|Thing $disabledBy ;

    /**
     * The reason why this service was disabled.
     * @var string|null
     */
    public string|null $disabledReason ;

    /**
     * The expiration date of this service (ISO 8601).
     * @var string|null
     */
    public string|null $expiresAt ;

    /**
     * Zitadel User Key identifier (POST /v2/users/{userId}/keys response).
     * Required for JWT assertion (`kid` header). Rotated by `services:rotate`.
     */
    public ?string $keyId = null ;

    /**
     * The full keyfile (RSA private key + metadata).
     *
     * Populated **only** in the response of POST /services,
     * POST /me/services, POST /services/{id}/rotate-key and
     * POST /me/services/{id}/rotate-key. Never persisted server-side
     * and never returned by GET endpoints.
     */
    public ?Keyfile $keyfile = null ;

    /**
     * The last IP address from which this service was seen.
     * @var string|null
     */
    public string|null $lastSeenIP ;

    /**
     * The last time this service was used (ISO 8601).
     * @var string|null
     */
    public string|null $lastUsedAt ;

    /**
     * Free-form metadata for this service.
     * @var object|array|null
     */
    public object|array|null $metadata ;

    /**
     * The direct permissions assigned to this service.
     * @var array<Permission>|null
     */
    #[HydrateWith( Permission::class ) ]
    public array|null $permissions ;

    /**
     * The number of direct permissions attached on this Service.
     * @var int|null
     */
    public int|null $permissionsCount ;

    /**
     * The policies assigned to this service (M2M authorization bundles).
     * @var array<Policy>|null
     */
    #[HydrateWith( Policy::class ) ]
    public array|null $policies ;

    /**
     * The number of policies attached on this Service.
     * @var int|null
     */
    public int|null $policiesCount ;

    /**
     * Whether this service is protected from deletion and deactivation.
     *
     * When true, neither admin nor owner can DELETE the document or PATCH
     * `active=false`. Server-written : the field is excluded from POST and
     * PATCH whitelists and can only be toggled via the dedicated CLI command
     * (`auth:services:protect` / `unprotect`) or the seed file.
     *
     * Use this flag for system-critical M2M services (cron sync, monitoring,
     * integrations) that must survive any UI mishandling.
     */
    public ?bool $protected = null ;
}