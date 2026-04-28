<?php

namespace xyz\oihana\schema\constants ;

use oihana\reflect\traits\ConstantsTrait ;

/**
 * Standard reasons used to populate `Session.revocationReason`.
 */
class SessionRevocationReason
{
    use ConstantsTrait ;

    /**
     * Session was revoked manually by an admin (audit / forensics action).
     */
    public const string ADMIN_REVOKED = 'admin_revoked' ;

    /**
     * Session was revoked through an explicit user logout action.
     */
    public const string LOGOUT = 'logout' ;

    /**
     * Session was revoked because the user account was disabled
     * (`user.status` flipped from `'active'` to `'disabled'`).
     */
    public const string USER_DISABLED = 'user_disabled' ;
}