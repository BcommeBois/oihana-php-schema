<?php

namespace xyz\oihana\schema\auth ;

use org\schema\Thing;
use xyz\oihana\schema\constants\traits\auth\KeyfileTrait;

/**
 * A keyfile JSON structure for PRIVATE_KEY_JWT API apps (Zitadel schema).
 *
 * Returned **once** by the API on application creation and key rotation,
 * never persisted server-side. The client M2M consumer must save the
 * keyfile locally and use it to sign short-lived JWT bearer assertions
 * exchanged at Zitadel's token endpoint for an access token.
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
class Keyfile extends Thing
{
    use KeyfileTrait ;

    /**
     * Zitadel app ID (= the `sub` claim of the resulting access token).
     */
    public ?string $appId = null ;

    /**
     * OAuth2 clientId of the application.
     * Used as `iss` and `sub` of the JWT bearer assertion.
     */
    public ?string $clientId = null ;

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
     * The keyfile type. Currently always `'application'` for API apps.
     * See {@see KeyfileType}.
     */
    public ?string $type = null ;
}

