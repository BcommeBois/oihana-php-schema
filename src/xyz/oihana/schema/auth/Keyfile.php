<?php

namespace xyz\oihana\schema\auth ;

use org\schema\Thing;
use xyz\oihana\schema\constants\traits\auth\KeyfileTrait;

/**
 * A keyfile JSON structure for PRIVATE_KEY_JWT M2M clients.
 *
 * Returned **once** by the API on service creation and key rotation,
 * never persisted server-side. The client M2M consumer must save the
 * keyfile locally and use it to sign short-lived JWT bearer assertions
 * exchanged at the IdP's token endpoint for an access token.
 *
 * The keyfile is auto-sufficient: it carries both the IdP-side
 * material (key, keyId, userId, clientId, type) and the connection
 * metadata (issuer, audience, scope, apiBaseUrl) so a third-party
 * developer can call the API without any additional configuration.
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
class Keyfile extends Thing
{
    use KeyfileTrait ;

    /**
     * The base URL of the API to call once a token has been acquired
     * (e.g. `https://api.example.com`).
     */
    public ?string $apiBaseUrl = null ;

    /**
     * The app ID (= the `sub` claim of the resulting access token).
     */
    public ?string $appId = null ;

    /**
     * The audience expected in the access token (typically the IdP
     * project identifier — Zitadel `projectId`).
     */
    public ?string $audience = null ;

    /**
     * OAuth2 clientId of the application.
     * Used as `iss` and `sub` of the JWT bearer assertion.
     */
    public ?string $clientId = null ;

    /**
     * The IdP issuer URL (e.g. `https://my-org.zitadel.cloud`).
     * The token endpoint is derived as `{issuer}/oauth/v2/token`.
     */
    public ?string $issuer = null ;

    /**
     * The RSA private key in PEM format (`-----BEGIN RSA PRIVATE KEY-----...`).
     * Used to sign the JWT bearer assertion.
     */
    public ?string $key = null ;

    /**
     * The keyId that identifies this specific key on the Zitadel side.
     * Used as `kid` in the JWT header — Zitadel resolves the matching
     * public key by this id when verifying the assertion.
     */
    public ?string $keyId = null ;

    /**
     * The OAuth2 scope to request at the token endpoint
     * (e.g. `openid profile urn:zitadel:iam:org:project:id:<projectId>:aud`).
     */
    public ?string $scope = null ;

    /**
     * The keyfile type.
     */
    public ?string $type = null ;

    /**
     * The IdP user identifier (= the `sub` claim of the resulting
     * access token). Used as both `iss` and `sub` of the JWT bearer
     * assertion.
     */
    public ?string $userId = null ;
}

