<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all PasswordReset custom properties.
 *
 * Properties already available via parent classes / other traits:
 * - AGENT, OBJECT, ACTION_STATUS, START_TIME, END_TIME (Schema.org Action)
 * - EMAIL (Schema.org Person)
 * - NAME, URL, IDENTIFIER (Schema.org Thing)
 *
 * Action-status values used by password resets (mapped to Schema.org ActionStatusType):
 * - ACTION_STATUS_PENDING   : awaiting consumption (link not yet clicked)
 * - ACTION_STATUS_CONSUMED  : code consumed, password effectively changed
 * - ACTION_STATUS_EXPIRED   : passed the expiration date without being consumed
 * - ACTION_STATUS_CANCELLED : revoked by admin or via user deletion cascade
 *
 * Note: This is a trait for use with the PasswordReset schema class and Prop class.
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 */
trait PasswordResetTrait
{
    const string EMAIL        = 'email' ;
    const string REDIRECT_URL = 'redirectUrl' ;
    const string SENT_AT      = 'sentAt'      ;
    const string TOKEN        = 'token'       ;

    // Action status values (string literals stored in the `actionStatus` field)

    const string ACTION_STATUS_CANCELLED = 'cancelled' ;
    const string ACTION_STATUS_CONSUMED  = 'consumed'  ;
    const string ACTION_STATUS_EXPIRED   = 'expired'   ;
    const string ACTION_STATUS_PENDING   = 'pending'   ;
}