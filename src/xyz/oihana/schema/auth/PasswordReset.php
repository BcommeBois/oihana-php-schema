<?php

namespace xyz\oihana\schema\auth;

use org\schema\actions\UpdateAction;

use xyz\oihana\schema\constants\traits\auth\PasswordResetTrait;
use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a password-reset request issued for a user.
 *
 * Password resets are created when a user (or an admin acting on their behalf,
 * or an anonymous "forgot password" caller) requests to change their
 * credentials. The API persists a SHA-256 hash of the verification code in
 * `token` and emails a custom MJML link to the user. When the user clicks
 * the link and submits a new password, the record is transitioned to
 * `consumed` and all active sessions of the user are revoked inline.
 *
 * The parent UpdateAction (and its ancestor Action) provides:
 * - agent        : the actor who triggered the request (user _key, or null for the public anonymous flow)
 * - object       : the target user (user _key)
 * - actionStatus : pending / consumed / expired / cancelled
 * - startTime    : creation date (ISO 8601)
 * - endTime      : expiration date (ISO 8601)
 *
 * Thing provides: name, description, identifier, active, owner, url,
 * created, modified.
 *
 * Constants can be referenced directly: PasswordReset::TOKEN, PasswordReset::SENT_AT, etc.
 *
 * @see PasswordResetTrait For the enumeration of custom PasswordReset properties.
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 */
class PasswordReset extends UpdateAction
{
    use PasswordResetTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    // ------- Properties

    /**
     * The destination email address of the reset request.
     * Stored for audit/display even if the target user's email changes later.
     * @var string|null
     */
    public string|null $email ;

    /**
     * The optional post-reset redirect URL.
     * Falls back to the default defined in [passwordReset] config when null.
     * @var string|null
     */
    public string|null $redirectUrl ;

    /**
     * The timestamp of the successful send (ISO 8601).
     * Remains null when the SMTP dispatch failed before the response.
     * @var string|null
     */
    public string|null $sentAt ;

    /**
     * The SHA-256 hash of the Zitadel verification code embedded in the reset link.
     * The clear code is only transmitted once (URL in email) and never stored.
     * @var string|null
     */
    public string|null $token ;
}