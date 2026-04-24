<?php

namespace xyz\oihana\schema\auth;

use org\schema\actions\InviteAction;

use xyz\oihana\schema\constants\traits\auth\InvitationTrait;
use xyz\oihana\schema\constants\Oihana;

/**
 * Represents an email invitation sent to a user to activate their account.
 *
 * Invitations are created by admins (via POST /users/{id}/invite) for users
 * who have not yet logged in (User::$activated === false). Once the invitee
 * completes the Zitadel password initialization flow and logs in for the
 * first time, the invitation is marked as accepted in CallbackController.
 *
 * The parent InviteAction (and its ancestor Action) provides:
 * - agent        : the admin who issued the invitation (user _key)
 * - object       : the invited user (user _key)
 * - actionStatus : pending / accepted / expired / cancelled
 * - startTime    : creation date (ISO 8601)
 * - endTime      : expiration date (ISO 8601)
 *
 * Thing provides: name, description, identifier, active, owner, url,
 * created, modified.
 *
 * Constants can be referenced directly: Invitation::TOKEN, Invitation::SENT_AT, etc.
 *
 * @see InvitationTrait For the enumeration of custom Invitation properties.
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 */
class Invitation extends InviteAction
{
    use InvitationTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    // ------- Properties

    /**
     * The destination email address of the invitation.
     * Stored for audit/display even if the invited user's email changes later.
     * @var string|null
     */
    public string|null $email ;

    /**
     * The optional post-activation redirect URL.
     * Falls back to the default defined in [invitations] config when null.
     * @var string|null
     */
    public string|null $redirectUrl ;

    /**
     * The timestamp of the last successful send attempt (ISO 8601).
     * Remains null when the SMTP dispatch failed and the invitation awaits a retry.
     * @var string|null
     */
    public string|null $sentAt ;

    /**
     * The number of successful send attempts, capped by [invitations].maxResend.
     * Starts at 0. Incremented only when the email dispatch succeeds.
     * @var int|null
     */
    public int|null $sentCount ;

    /**
     * The SHA-256 hash of the Zitadel verification code embedded in the activation link.
     * The clear code is only transmitted once (URL in email) and never stored.
     * @var string|null
     */
    public string|null $token ;
}
