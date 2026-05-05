<?php

namespace xyz\oihana\schema\auth;

use oihana\reflect\attributes\HydrateWith;

use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\auth\ApplicationsTrait;

/**
 * Represents a client application (PKCE, M2M, public) that connects to a WebAPI.
 *
 * Applications are OAuth2 clients created by admins or users (self-service).
 * Each application has an owner (user), one or more scopes (permission groups),
 * and optional direct permissions.
 *
 * @see Keyfile
 * @see Permission
 * @see Policy
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
class Application extends Thing
{
    use ApplicationsTrait ;

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
     * The OAuth2/OIDC client ID (from Zitadel).
     * @var string|null
     */
    public string|null $clientId ;

    /**
     * The user (or system) who created this application.
     * @var string|Thing|null
     */
    public string|null|Thing $createdBy ;

    /**
     * Whether this is the default application for the API.
     * @var bool|null
     */
    public bool|null $default ;

    /**
     * The date this application was disabled (ISO 8601).
     * @var string|null
     */
    public string|null $disabledAt ;

    /**
     * The user (or system) who disabled this application.
     * @var string|Thing|null
     */
    public string|null|Thing $disabledBy ;

    /**
     * The reason why this application was disabled.
     * @var string|null
     */
    public string|null $disabledReason ;

    /**
     * The expiration date of this application (ISO 8601).
     * @var string|null
     */
    public string|null $expiresAt ;

    /**
     * The keyId of the currently active key for this M2M application.
     * Server-written — set on creation and rotated through the
     * `/applications/{id}/rotate-key` endpoint. Persisted in Arango.
     */
    public ?string $keyId = null ;

    /**
     * The full keyfile (RSA private key + metadata).
     *
     * Populated **only** in the response of POST /applications,
     * POST /me/applications, POST /applications/{id}/rotate-key and
     * POST /me/applications/{id}/rotate-key. Never persisted server-side
     * and never returned by GET endpoints.
     */
    public ?Keyfile $keyfile = null ;

    /**
     * The last IP address from which this application was seen.
     * @var string|null
     */
    public string|null $lastSeenIP ;

    /**
     * The last time this application was used (ISO 8601).
     * @var string|null
     */
    public string|null $lastUsedAt ;

    /**
     * Free-form metadata for this application.
     * @var object|array|null
     */
    public object|array|null $metadata ;

    /**
     * The direct permissions assigned to this application.
     * @var array<Permission>|null
     */
    #[HydrateWith( Permission::class ) ]
    public array|null $permissions ;

    /**
     * The number of direct permissions attached on this Application.
     * @var int|null
     */
    public int|null $permissionsCount ;

    /**
     * The policies assigned to this application (M2M authorization bundles).
     * @var array<Policy>|null
     */
    #[HydrateWith( Policy::class ) ]
    public array|null $policies ;

    /**
     * The number of policies attached on this Application.
     * @var int|null
     */
    public int|null $policiesCount ;

    /**
     * Whether this application is protected from deletion and deactivation.
     *
     * When true, neither admin nor owner can DELETE the document or PATCH
     * `active=false`. Server-written : the field is excluded from POST and
     * PATCH whitelists and can only be toggled via the dedicated CLI command
     * (`auth:applications:protect` / `unprotect`) or the seed file.
     *
     * Distinct from `default` (which marks the singleton API-default app) :
     * `protected` is a broader, multi-instance flag for system-critical M2M
     * apps (cron sync, monitoring, integrations) that must survive any UI
     * mishandling.
     */
    public ?bool $protected = null ;
}
