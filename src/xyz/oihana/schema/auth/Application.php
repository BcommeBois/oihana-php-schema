<?php

namespace xyz\oihana\schema\auth;

use oihana\reflect\attributes\HydrateWith;

use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a client application (PKCE, M2M, public) that connects to a WebAPI.
 *
 * Applications are OAuth2 clients created by admins or users (self-service).
 * Each application has an owner (user), one or more scopes (permission groups),
 * and optional direct permissions.
 *
 * Thing provides: name, description, identifier, additionalType, active, owner,
 * url, created, modified.
 *
 * @see Permission
 * @see Scope
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 */
class Application extends Thing
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The OAuth2/OIDC client ID (from Zitadel).
     * @var string|null
     */
    public string|null $clientId ;

    /**
     * Whether this is the default application for the API.
     * @var bool|null
     */
    public bool|null $default ;

    /**
     * The expiration date of this application (ISO 8601).
     * @var string|null
     */
    public string|null $expiresAt ;

    /**
     * IP whitelist using glob patterns (fnmatch).
     * @var array|null
     */
    public array|null $allowedIPs ;

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
     * The scopes assigned to this application.
     * @var array<Scope>|null
     */
    #[HydrateWith( Scope::class ) ]
    public array|null $scopes ;

    /**
     * The number of scopes attached on this Application.
     * @var int|null
     */
    public int|null $scopesCount ;

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
}
