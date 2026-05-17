<?php

namespace xyz\oihana\schema\constants ;

use oihana\reflect\traits\ConstantsTrait ;

/**
 * Defines the standard revocation reason identifiers used to populate
 * the `Session.revocationReason` property.
 *
 * These constants provide a normalized and auditable vocabulary for
 * describing why a session, refresh token, or authentication context
 * has been invalidated by the system.
 *
 * Typical use cases include:
 *
 * - Authentication and authorization workflows
 * - Session lifecycle tracking
 * - Security audits and incident analysis
 * - Token revocation middleware
 * - User account management events
 * - Logout and forced sign-out flows
 *
 * Example:
 *
 * ```php
 * $session->revocationReason = SessionRevocationReason::LOGOUT ;
 * ```
 *
 * @package xyz\oihana\schema\constants
 *
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class SessionRevocationReason
{
    use ConstantsTrait ;

    /**
     * Indicates that the session was revoked manually by an administrator.
     *
     * This reason is generally used during moderation, compliance,
     * security investigations, account recovery operations, or
     * forensic audit procedures.
     */
    public const string ADMIN_REVOKED = 'admin_revoked' ;

    /**
     * Indicates that the session was revoked as part of an emergency
     * security response, typically triggered by an incident, a confirmed
     * compromise, or an automated threat-mitigation workflow.
     *
     * This reason is reserved for high-severity events where sessions
     * must be terminated immediately, independently of the standard
     * user-driven or administrative revocation flows.
     *
     * Typical use cases include:
     *
     * - Suspected or confirmed account takeover
     * - Credential leak or token exposure detected by monitoring
     * - Automated revocation initiated by an intrusion-detection system
     * - Forced global sign-out following a security breach
     * - Incident-response playbooks requiring immediate session kill
     *
     * Unlike ADMIN_REVOKED, this revocation is not a routine moderation
     * action but an exceptional measure, and should be surfaced
     * accordingly in audit trails and security dashboards.
     */
    public const string EMERGENCY_REVOKE = 'emergency_revoke' ;

    /**
     * Indicates that the session was revoked after an explicit logout
     * action initiated by the authenticated user.
     *
     * This is the standard revocation reason for voluntary sign-out
     * operations.
     */
    public const string LOGOUT = 'logout' ;

    /**
     * Indicates that the session was invalidated because the user's
     * authentication tokens became globally invalid.
     *
     * This typically occurs when the application updates the
     * `tokensInvalidBefore` cutoff timestamp on the user entity,
     * causing all access or refresh tokens issued before that date
     * to be rejected.
     *
     * This reason may be surfaced by authentication middleware in
     * `401 Unauthorized` responses when an access token `iat`
     * (issued-at timestamp) predates the invalidation cutoff.
     */
    public const string TOKENS_REVOKED = 'tokens_revoked' ;

    /**
     * Indicates that the session was revoked because the associated
     * user account was permanently removed from the system.
     */
    public const string USER_DELETED = 'user_deleted' ;

    /**
     * Indicates that the session was revoked because the associated
     * user account was disabled or suspended.
     *
     * This commonly occurs when the user's status changes from
     * `'active'` to `'disabled'`, preventing any further authenticated
     * access until the account is restored.
     */
    public const string USER_DISABLED = 'user_disabled' ;

    /**
     * Indicates that the session was revoked directly by the user
     * outside of a standard logout flow.
     *
     * This reason is typically used when a user explicitly invalidates
     * one or more active sessions from an account security interface,
     * such as a "Sign out from other devices" or "Revoke session"
     * action.
     *
     * Unlike Logout, this revocation may target remote or
     * previously established sessions without terminating the current
     * authenticated context.
     */
    public const string USER_REVOKED = 'user_revoked' ;
}
