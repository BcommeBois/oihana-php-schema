<?php

namespace xyz\oihana\schema\auth;

use xyz\oihana\schema\constants\traits\auth\PendingRevocationTrait;

use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a deferred revocation that could not be applied to its target
 * provider at the time the local revocation was committed.
 *
 * Typical use case: an admin revokes a user's sessions; the local soft-delete
 * succeeds in ArangoDB but the propagation call to the identity provider
 * (e.g. Zitadel via Sessions API v2) fails (timeout, 5xx, network). Instead
 * of leaving the provider in an inconsistent state, the revocation intent is
 * persisted here and replayed asynchronously by:
 *
 * - a periodic background command (`auth:sessions:retry-zitadel-revocations`);
 * - the local cache flush flow (`bun flush`);
 * - opportunistic retries on next login or next admin revocation for the same
 *   user (when the connectivity to the provider is known to be working).
 *
 * The schema is intentionally provider-agnostic: the `provider` discriminator
 * lets future cleanup pipelines (Magento token invalidation, Auth0, custom
 * IdPs) reuse the same collection without a parallel schema.
 *
 * Thing provides: _key, id, identifier, name, active, owner, created, modified.
 *
 * Constants can be referenced directly: PendingRevocation::TARGET_ID,
 * PendingRevocation::ATTEMPTS, etc.
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 */
class PendingRevocation extends Thing
{
    use PendingRevocationTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Number of times the retry pipeline already attempted to replay the
     * revocation. Bumped on every replay attempt. Capped by the retry
     * command's `--max-attempts` (default 5): past this threshold the row
     * is kept for manual inspection.
     * @var int|null
     */
    public int|null $attempts ;

    /**
     * The ISO 8601 UTC timestamp of the most recent retry attempt.
     * Null when the row was just inserted and has not been replayed yet.
     * @var string|null
     */
    public string|null $lastAttemptAt ;

    /**
     * Short message captured from the last failed retry attempt
     * (provider error code / exception message). Plain text, no PII.
     * @var string|null
     */
    public string|null $lastError ;

    /**
     * The identity provider that owns the target resource. Examples:
     * `"zitadel"`, `"magento"`, `"auth0"`. Used by the retry pipeline to
     * route the replay to the matching client trait.
     * @var string|null
     */
    public string|null $provider ;

    /**
     * Free-form reason describing why this revocation was queued.
     * Examples: `"admin_revoke"`, `"user_logout"`, `"password_change"`,
     * `"user_disabled"`. Mirrors the convention used by
     * `Session.revocationReason`.
     * @var string|null
     */
    public string|null $reason ;

    /**
     * The identifier of the resource to revoke on the provider side.
     * Examples: a Zitadel `sessionId`, a Magento `tokenId`. Opaque to the
     * local pipeline, only meaningful for the provider's API.
     * @var string|null
     */
    public string|null $targetId ;

    /**
     * The category of resource being revoked. Examples: `"session"`,
     * `"token"`, `"user"`. Lets a single collection hold heterogeneous
     * revocation intents while keeping the retry logic strongly typed.
     * @var string|null
     */
    public string|null $targetType ;

    /**
     * The provider-side user identifier (e.g. Zitadel `sub` claim). Used
     * by retry hooks to scope opportunistic retries to a single user
     * during a login or a fresh admin revocation event.
     * @var string|null
     */
    public string|null $userIdentifier ;

    /**
     * The ArangoDB `_key` of the local user document that owns this
     * revocation intent. Useful for audit (joining back to the user row
     * after anonymisation of `userIdentifier`) and for opportunistic
     * cleanup on user delete cascade.
     * @var string|null
     */
    public string|null $userKey ;
}