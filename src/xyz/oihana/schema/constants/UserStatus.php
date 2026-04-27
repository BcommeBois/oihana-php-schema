<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Defines the lifecycle status of a User account, distinct from the
 * historical `activated` flag (which records whether the user has
 * completed their first login).
 *
 * Where `activated` is immutable once true (audit fact), `status` is
 * a mutable admin-controlled state that gates whether the account can
 * currently be used to authenticate. The login pipeline must refuse
 * any session creation when `status !== UserStatus::ACTIVE`.
 *
 * Designed as an extensible enum string: future values may include
 * `suspended` (auto-locked after security signal) or `archived`
 * (post-anonymization placeholder), without breaking storage or
 * filtering on existing `active`/`disabled` documents.
 *
 * @package xyz\oihana\schema\constants
 * @category Security
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class UserStatus
{
    use ConstantsTrait ;

    /**
     * The account is active and can authenticate normally.
     */
    public const string ACTIVE = 'active' ;

    /**
     * The account has been disabled by an administrator.
     * Login is refused; sessions are revoked.
     */
    public const string DISABLED = 'disabled' ;
}