<?php

namespace xyz\oihana\schema\auth;

use xyz\oihana\schema\constants\traits\auth\SessionTrait;

use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a user session stored in ArangoDB.
 *
 * Sessions track active connections (IP, user-agent, device) and are created
 * at PKCE login, validated on each authenticated request, and revoked at logout.
 *
 * Thing provides: name, description, identifier, active, owner, url, created, modified.
 *
 * Constants can be referenced directly: Session::USER_ID, Session::TOKEN_HASH, etc.
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 */
class Session extends Thing
{
    use SessionTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    // ------- Properties

    /**
     * The Zitadel OIDC client that emitted the access token (`azp` claim).
     * Discriminates sessions when the same user logs in from several apps
     * sharing the same Zitadel project (e.g. API /login vs external NextJS).
     * @var string|null
     */
    public string|null $clientId ;

    /**
     * Whether this is the current session (set dynamically, not stored).
     * @var bool|null
     */
    public bool|null $current ;

    /**
     * The session expiration date (ISO 8601).
     * @var string|null
     */
    public string|null $expiresAt ;

    /**
     * The client IP address (canonicalized).
     * @var string|null
     */
    public string|null $ip ;

    /**
     * Free-form metadata for this session.
     * @var object|array|null
     */
    public object|array|null $metadata ;

    /**
     * The date when this session was revoked (ISO 8601).
     * @var string|null
     */
    public string|null $revokedAt ;

    /**
     * The SHA-256 hash of the access token.
     * @var string|null
     */
    public string|null $tokenHash ;

    /**
     * The browser/client User-Agent string.
     * @var string|null
     */
    public string|null $userAgent ;

    /**
     * The ArangoDB _key of the user who owns this session.
     * @var string|null
     */
    public string|null $userId ;
}
