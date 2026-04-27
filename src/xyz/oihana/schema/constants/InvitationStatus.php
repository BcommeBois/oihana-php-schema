<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Defines the materialized state of the latest invitation associated
 * with a User account.
 *
 * This value is stored directly on the User document (field
 * `invitationStatus`) for cheap filtering and sorting on the admin
 * users list — avoiding an AQL sub-query against the invitations
 * collection on every read.
 *
 * Conceptually mirrors a subset of schema.org's `ActionStatusType`
 * but with finer-grained semantics required by the application:
 * `expired` and `cancelled` would both collapse into `FailedActionStatus`
 * upstream, losing UX information. The matching is roughly:
 *
 * - `none`      ↔ no invitation ever sent (user created dormant)
 * - `pending`   ↔ ActionStatusType::ActiveActionStatus
 * - `accepted`  ↔ ActionStatusType::CompletedActionStatus
 * - `expired`   ↔ ActionStatusType::FailedActionStatus (timeout)
 * - `cancelled` ↔ ActionStatusType::FailedActionStatus (admin)
 *
 * The Invitation document itself uses schema.org `actionStatus` on
 * the row directly; this enum is for the user-side projection only.
 *
 * @package xyz\oihana\schema\constants
 * @category Security
 * @see \org\schema\enumerations\status\ActionStatusType
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class InvitationStatus
{
    use ConstantsTrait ;

    /**
     * The invitation has been consumed at the user's first successful login.
     */
    public const string ACCEPTED = 'accepted' ;

    /**
     * The invitation was soft-cancelled by an admin (DELETE).
     */
    public const string CANCELLED = 'cancelled' ;

    /**
     * The invitation reached `endTime` without being consumed.
     */
    public const string EXPIRED = 'expired' ;

    /**
     * The user account was created without dispatching an invitation.
     * Only an admin re-invite or password-reset can move it forward.
     */
    public const string NONE = 'none' ;

    /**
     * An invitation has been sent and is awaiting acceptance.
     */
    public const string PENDING = 'pending' ;
}