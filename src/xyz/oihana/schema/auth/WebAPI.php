<?php

namespace xyz\oihana\schema\auth;

use oihana\reflect\attributes\HydrateWith;

use org\schema\WebAPI as SchemaWebAPI;

use xyz\oihana\schema\constants\JWTAlgorithm;
use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a Web API resource with OAuth2 authentication capabilities.
 *
 * This class extends the Schema.org `WebAPI` type with OAuth2-specific
 * configuration options, including JSON Web Token (JWT) signing, token lifetimes,
 * RBAC (Role-Based Access Control), and permission management.
 *
 * Features:
 * - JWT Signing Algorithm configuration (`$algorithm`)
 * - Maximum Access Token lifetime (`$maximumAccessTokenExpiration`)
 * - Implicit/Hybrid Flow Access Token lifetime (`$implicitHybridTokenLifetime`)
 *   for tokens issued to client-side applications
 * - Optional offline access via refresh tokens (`$allowOfflineAccess`)
 * - Optional skipping of user consent for first-party applications (`$allowSkipUserConsent`)
 * - Permissions and scopes management (`$permissions` and `$permissionsCount`)
 * - RBAC support and including permissions in the access token (`$rbac`, `$scopeHasPermission`)
 *
 * Default constants:
 * - DEFAULT_ALGORITHM: `RS256`
 * - DEFAULT_TOKEN_EXPIRATION: 86400 seconds (24 hours)
 * - DEFAULT_IMPLICIT_HYBRID_TOKEN_LIFETIME: 7200 seconds (2 hours)
 *
 * Usage example:
 * ```php
 * use xyz\oihana\schema\auth\WebAPI;
 *
 * $api = new WebAPI();
 * $api->algorithm = WebAPI::DEFAULT_ALGORITHM;
 * $api->allowOfflineAccess = true;
 * $api->implicitHybridTokenLifetime = 3600;
 * $api->permissions = ['read', 'write'];
 * $api->rbac = true;
 * $api->scopeHasPermission = true;
 * ```
 *
 * @see JWTAlgorithm
 *
 * @package xyz\oihana\schema\auth
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class WebAPI extends SchemaWebAPI
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The default JSON Web Token (JWT) Signing Algorithm.
     */
    public const string DEFAULT_ALGORITHM = JWTAlgorithm::RS256 ;

    /**
     * The default Implicit/Hybrid Flow Access Token Lifetime value in seconds.
     */
    public const int DEFAULT_IMPLICIT_HYBRID_TOKEN_LIFETIME = 7200 ;

    /**
     * The default Maximum Access Token Lifetime value in seconds.
     */
    public const int DEFAULT_TOKEN_EXPIRATION = 86400 ;

    // ------ Properties

    /**
     * The JSON Web Token (JWT) Signing Algorithm (Default RS256).
     * @var string|null
     */
    public null|string $algorithm ;

    /**
     * If this setting is enabled, will allow applications to ask for Refresh Tokens for this API.
     * @var bool|null
     */
    public bool|null $allowOfflineAccess ;

    /**
     * If this setting is enabled, this API will skip user consent for applications flagged as First Party.
     * @var bool|null
     */
    public bool|null $allowSkipUserConsent ;

    /**
     * The Implicit/Hybrid Flow Access Token Lifetime.
     *
     * Time until an access token issued for this API, using either the implicit or hybrid flow, will expire.
     * Cannot exceed the maximum access token lifetime.
     */
    public int|null $implicitHybridTokenLifetime ;

    /**
     * The maximum Access Token lifetime in seconds.
     * Time until an access token issued for this API will expire.
     */
    public int|null $maximumAccessTokenExpiration ;

    /**
     * Define the permissions (scopes) that this API uses.
     * @var array<Permission>|null
     */
    #[HydrateWith( Permission::class ) ]
    public array|null $permissions ;

    /**
     * The number of permissions attached on this API.
     * @var int|null
     */
    public int|null $permissionsCount ;

    /**
     * Indicates if the RBAC is enabled.
     * @var bool|null
     */
    public bool|null $rbac  ;

    /**
     * If this setting is enabled, the Permissions claim will be added to the access token.
     * Only available if RBAC is enabled for this API.
     */
    public bool|null $scopeHasPermission ;
}