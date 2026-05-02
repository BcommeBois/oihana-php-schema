<?php

namespace xyz\oihana\schema\constants\traits\auth;

/**
 * The enumeration of all User custom properties added by this project.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
trait UserTrait
{
    use ApplicationsTrait ,
        PermissionsTrait  ,
        RolesTrait        ;

    const string ACTIVATED                      = 'activated'     ;
    const string APP_META_DATA                  = 'appMetadata'  ;
    const string BLOCKED_FOR                    = 'blockedFor' ;
    const string DEVICES                        = 'devices' ;
    const string FIRST_LOGIN_AT                 = 'firstLoginAt'  ;
    const string INVITATION_STATUS              = 'invitationStatus' ;
    const string LAST_LOGIN                     = 'lastLogin' ;
    const string LOGINS_COUNT                   = 'loginsCount' ;
    const string MAX_LEVEL                      = 'maxLevel' ;
    const string METADATA                       = 'metadata' ;
    const string PENDING_EMAIL                  = 'pendingEmail' ;
    const string PENDING_EMAIL_CODE_EXPIRES_AT  = 'pendingEmailCodeExpiresAt' ;
    const string PENDING_EMAIL_CODE_HASH        = 'pendingEmailCodeHash' ;
    const string PENDING_EMAIL_SINCE            = 'pendingEmailSince' ;
    const string SIGNED_UP                      = 'signedUp' ;
    const string STATUS                         = 'status' ;
}