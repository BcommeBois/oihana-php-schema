<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all Invitation custom properties.
 *
 * Properties already available via parent classes / other traits:
 * - AGENT, OBJECT, ACTION_STATUS, START_TIME, END_TIME (Schema.org Action)
 * - EMAIL (Schema.org Person)
 * - NAME, URL, IDENTIFIER (Schema.org Thing)
 * - METADATA (ApplicationTrait)
 *
 * Action-status values used by invitations (mapped to Schema.org ActionStatusType):
 * - ACTION_STATUS_PENDING   : pending acceptance
 * - ACTION_STATUS_ACCEPTED  : user has activated (first login succeeded)
 * - ACTION_STATUS_EXPIRED   : passed the expiration date
 * - ACTION_STATUS_CANCELLED : revoked by admin or via user deletion cascade
 *
 * Note: This is a trait for use with the Invitation schema class and Prop class.
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 */
trait InvitationTrait
{
    const string EMAIL        = 'email' ;
    const string REDIRECT_URL = 'redirectUrl' ;
    const string SENT_AT      = 'sentAt'      ;
    const string SENT_COUNT   = 'sentCount'   ;
    const string TOKEN        = 'token'       ;

    // Action status values (string literals stored in the `actionStatus` field)

    const string ACTION_STATUS_ACCEPTED  = 'accepted'  ;
    const string ACTION_STATUS_CANCELLED = 'cancelled' ;
    const string ACTION_STATUS_EXPIRED   = 'expired'   ;
    const string ACTION_STATUS_PENDING   = 'pending'   ;
}
