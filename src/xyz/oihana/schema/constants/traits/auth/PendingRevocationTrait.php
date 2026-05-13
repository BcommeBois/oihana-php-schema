<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all PendingRevocation properties.
 *
 * Properties already available via Thing / other traits:
 * - ACTIVE, IDENTIFIER, OWNER, NAME, CREATED, MODIFIED, ID (Schema.org)
 *
 * Note: This is a trait for use with Prop class. To reference constants
 * directly in controllers, use the PendingRevocation schema class or Prop.
 *
 * @package oihana\schema\constants\traits\auth
 * @author  Marc Alcaraz
 */
trait PendingRevocationTrait
{
    const string ATTEMPTS         = 'attempts'         ;
    const string LAST_ATTEMPT_AT  = 'lastAttemptAt'    ;
    const string LAST_ERROR       = 'lastError'        ;
    const string PROVIDER         = 'provider'         ;
    const string REASON           = 'reason'           ;
    const string TARGET_ID        = 'targetId'         ;
    const string TARGET_TYPE      = 'targetType'       ;
    const string USER_IDENTIFIER  = 'userIdentifier'   ;
    const string USER_KEY         = 'userKey'          ;
}