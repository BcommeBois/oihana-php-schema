<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * Defines the complete set of custom property names associated with
 * authenticated user entities in the application domain.
 *
 * This trait centralizes all user-related schema keys used by the
 * authentication, authorization, identity, session-management,
 * invitation, and RBAC layers of the project.
 *
 * The constants declared here are intended to:
 *
 * - Normalize property naming across the codebase
 * - Avoid hardcoded string literals
 * - Improve IDE auto-completion and refactoring safety
 * - Provide a single source of truth for custom user attributes
 * - Facilitate schema serialization and hydration
 * - Ensure consistency between models, DTOs, APIs, and persistence layers
 *
 * This trait aggregates several specialized traits related to:
 *
 * - Applications
 * - Permissions
 * - Protected resources
 * - Roles
 * - External or internal services
 *
 * Typical usage:
 *
 * ```php
 * $user[ UserTrait::STATUS ] = 'active' ;
 * ```
 *
 * @package xyz\oihana\schema\constants\traits
 *
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
trait UserTrait
{
    use ApplicationsTrait      ,
        PermissionsTrait       ,
        ProtectedResourceTrait ,
        RolesTrait             ,
        ServicesTrait          ;

    /**
     * Indicates whether the user account has completed its activation flow.
     *
     * This flag is generally set after the first successful authentication
     * or after an email/account verification workflow.
     *
     * Related model property:
     *
     * ```php
     * public bool|null $activated ;
     * ```
     */
    const string ACTIVATED = 'activated' ;

    /**
     * Read-only metadata associated with the user.
     *
     * Commonly used for:
     *
     * - Roles
     * - Permission snapshots
     * - VIP flags
     * - Internal application configuration
     * - Derived authorization information
     *
     * Related model property:
     *
     * ```php
     * public array|object|null $appMetadata ;
     * ```
     */
    const string APP_META_DATA = 'appMetadata' ;

    /**
     * Indicates why or for which scope the user is blocked.
     *
     * This field may contain:
     *
     * - API restrictions
     * - Application-specific bans
     * - Temporary suspension reasons
     * - Security mitigation contexts
     *
     * Related model property:
     *
     * ```php
     * public array|string|null $blockedFor ;
     * ```
     */
    const string BLOCKED_FOR = 'blockedFor' ;

    /**
     * Collection of known or trusted user devices.
     *
     * Used to:
     *
     * - Manage refresh-token associations
     * - Revoke sessions per device
     * - Force reauthentication
     * - Track authenticated environments
     *
     * Related model property:
     *
     * ```php
     * public array|null $devices ;
     * ```
     */
    const string DEVICES = 'devices' ;

    /**
     * Timestamp of the user's first successful login.
     *
     * Immutable audit-oriented field generally stored in ISO 8601 format.
     *
     * Related model property:
     *
     * ```php
     * public string|null $firstLoginAt ;
     * ```
     */
    const string FIRST_LOGIN_AT = 'firstLoginAt' ;

    /**
     * Materialized lifecycle status of the latest invitation associated
     * with the user.
     *
     * Used by administrative interfaces to expose invitation state
     * without requiring additional collection lookups.
     *
     * Typical values include:
     *
     * - pending
     * - accepted
     * - cancelled
     * - expired
     * - revoked
     *
     * Related model property:
     *
     * ```php
     * public string|null $invitationStatus ;
     * ```
     */
    const string INVITATION_STATUS = 'invitationStatus' ;

    /**
     * Timestamp of the user's most recent successful authentication.
     *
     * Related model property:
     *
     * ```php
     * public string|null $lastLogin ;
     * ```
     */
    const string LAST_LOGIN = 'lastLogin' ;

    /**
     * Total number of successful user authentications.
     *
     * Commonly used for:
     *
     * - Analytics
     * - User activity indicators
     * - Security heuristics
     * - Administrative dashboards
     *
     * Related model property:
     *
     * ```php
     * public int|null $loginsCount ;
     * ```
     */
    const string LOGINS_COUNT = 'loginsCount' ;

    /**
     * Maximum role level assigned to the user across all associated roles.
     *
     * Materialized helper field mainly used by administrative interfaces
     * to expose hierarchy hints and UX restrictions.
     *
     * Related model property:
     *
     * ```php
     * public int|null $maxLevel ;
     * ```
     */
    const string MAX_LEVEL = 'maxLevel' ;

    /**
     * Read/write metadata associated with the user.
     *
     * Typically contains:
     *
     * - User preferences
     * - UI customization
     * - Profile extensions
     * - Domain-specific settings
     *
     * Related model property:
     *
     * ```php
     * public array|object|null $metadata ;
     * ```
     */
    const string METADATA = 'metadata' ;

    /**
     * Email address currently awaiting verification.
     *
     * Used during email-change workflows where the previously verified
     * email remains authoritative until confirmation succeeds.
     *
     * Related model property:
     *
     * ```php
     * public string|null $pendingEmail ;
     * ```
     */
    const string PENDING_EMAIL = 'pendingEmail' ;

    /**
     * Expiration timestamp of the verification code associated with
     * the pending email workflow.
     *
     * Usually stored as an ISO 8601 string.
     *
     * Related model property:
     *
     * ```php
     * public string|null $pendingEmailCodeExpiresAt ;
     * ```
     */
    const string PENDING_EMAIL_CODE_EXPIRES_AT = 'pendingEmailCodeExpiresAt' ;

    /**
     * Secure hash of the verification code associated with the
     * pending email verification workflow.
     *
     * The raw verification code must never be persisted.
     *
     * Related model property:
     *
     * ```php
     * public string|null $pendingEmailCodeHash ;
     * ```
     */
    const string PENDING_EMAIL_CODE_HASH = 'pendingEmailCodeHash' ;

    /**
     * Timestamp indicating when the pending email workflow started.
     *
     * Usually stored as an ISO 8601 string.
     *
     * Related model property:
     *
     * ```php
     * public string|null $pendingEmailSince ;
     * ```
     */
    const string PENDING_EMAIL_SINCE = 'pendingEmailSince' ;

    /**
     * Indicates whether the user completed the signup workflow.
     *
     * Related model property:
     *
     * ```php
     * public string|null $signedUp ;
     * ```
     */
    const string SIGNED_UP = 'signedUp' ;

    /**
     * Lifecycle status of the user account.
     *
     * This status controls whether authentication and access are allowed.
     *
     * Typical values include:
     *
     * - active
     * - disabled
     * - suspended
     * - pending
     *
     * Related model property:
     *
     * ```php
     * public string|null $status ;
     * ```
     */
    const string STATUS = 'status' ;

    /**
     * Epoch-seconds timestamp defining the authentication revocation
     * cutoff for all access tokens issued to the user.
     *
     * This property is updated during bulk session revocation flows,
     * including:
     *
     * - Administrative "revoke all sessions" actions
     * - Self-service "log out everywhere" operations
     * - Security incident mitigation procedures
     *
     * During authentication, the middleware compares this value against
     * the JWT `iat` (issued-at) claim:
     *
     * ```text
     * token.iat < tokensInvalidBefore
     * ```
     *
     * If true, the token is rejected with:
     *
     * - HTTP status `401 Unauthorized`
     * - revocation reason `tokens_revoked`
     *
     * even when the token signature and expiration are still valid.
     *
     * The value is intentionally stored as an integer epoch timestamp
     * rather than ISO 8601 for extremely fast integer comparisons during
     * authenticated API requests.
     *
     * A `null` value means no global token revocation cutoff currently
     * applies to the user.
     *
     * Related model property:
     *
     * ```php
     * public int|null $tokensInvalidBefore ;
     * ```
     */
    const string TOKENS_INVALID_BEFORE = 'tokensInvalidBefore' ;
}