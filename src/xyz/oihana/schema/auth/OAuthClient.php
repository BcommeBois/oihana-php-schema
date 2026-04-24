<?php

namespace xyz\oihana\schema\auth;

use xyz\oihana\schema\constants\traits\auth\OAuthClientTrait;
use xyz\oihana\schema\constants\Oihana;

use org\schema\Thing;


/**
 * Represents an OAuth2/OIDC client application known to Zitadel.
 *
 * Materialized mirror of Zitadel-side applications — used to resolve a
 * raw Zitadel `clientId` (e.g. `365491048845237649`) to a human-readable
 * label at session creation time, without hitting the Zitadel Management
 * API on every authenticated request.
 *
 * The collection is populated lazily on the first session seen with an
 * unknown `clientId`, and can be refreshed in bulk by the
 * `command:auth:zitadel:sync:apps` CLI.
 *
 * Thing provides: name, description, identifier, active, created, modified.
 *
 * Constants can be referenced directly: OAuthClient::CLIENT_ID, OAuthClient::APP_ID.
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 */
class OAuthClient extends Thing
{
    use OAuthClientTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    // ------- Properties

    /**
     * The Zitadel internal application ID (stable, opaque).
     *
     * Distinct from {@see self::$clientId} which is the OIDC/OAuth2 public
     * identifier. The appId is required for Zitadel Management API calls
     * targeting this application (deactivate, delete, regenerate secret).
     *
     * @var string|null
     */
    public string|null $appId ;

    /**
     * The OAuth2/OIDC client ID (from Zitadel).
     * @var string|null
     */
    public string|null $clientId ;
}
