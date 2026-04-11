<?php
namespace xyz\oihana\schema\auth;

use org\schema\creativeWork\SoftwareApplication;
use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a client application (SPA, native, M2M, web) that connects to a WebAPI.
 *
 * Maps to Schema.org WebApplication (> SoftwareApplication > CreativeWork).
 * Stores OAuth2/OIDC client configuration for Zitadel integration.
 *
 * @see https://schema.org/WebApplication
 * @package xyz\oihana\schema\auth
 * @author  Marc Alcaraz
 * @since 1.0.2
 */
class WebApplication extends SoftwareApplication
{
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Indicates if the application is active.
     * @var bool|null
     */
    public bool|null $active ;

    /**
     * The API domain identifier this application accesses (ex: 'commerce-api').
     * Maps to the owning WebAPI's identifier, used as Casbin domain.
     * @var string|null
     */
    public string|null $apiIdentifier ;

    /**
     * The type of application: 'spa', 'native', 'm2m', 'web'.
     * @var string|null
     */
    public string|null $applicationType ;

    /**
     * The OAuth2/OIDC client ID (from Zitadel).
     * @var string|null
     */
    public string|null $clientId ;

    /**
     * The post-logout redirect URIs.
     * @var array|null
     */
    public array|null $postLogoutRedirectUris ;

    /**
     * The OAuth2 redirect URIs for this application.
     * @var array|null
     */
    public array|null $redirectUris ;
}