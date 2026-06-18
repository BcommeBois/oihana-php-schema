<?php

namespace xyz\oihana\schema\constants\traits\business;

/**
 * Defines the custom property names of the
 * {@see \xyz\oihana\schema\business\BusinessIdentity} schema entity.
 *
 * Centralizing these keys avoids hardcoded string literals and provides a
 * single source of truth for hydration and serialization.
 *
 * > **Note** — this trait is intentionally **not** aggregated into the global
 * > {@see \xyz\oihana\schema\constants\Oihana} constants class: its `SUBJECT`
 * > key collides with the already-aggregated
 * > {@see \xyz\oihana\schema\constants\traits\auth\PermissionTrait::SUBJECT}.
 * > It is composed directly by the `BusinessIdentity` entity instead.
 *
 * Typical usage:
 *
 * ```php
 * $identity[ BusinessIdentityTrait::ROLE ] = 'seller' ;
 * ```
 *
 * @package xyz\oihana\schema\constants\traits\business
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
trait BusinessIdentityTrait
{
    /**
     * The organization the {@see BusinessIdentityTrait::SUBJECT} belongs to.
     *
     * Optional — typically set when the subject is a person acting for an
     * organization (e.g. a customer contact and its customer organization).
     *
     * Related model property:
     *
     * ```php
     * public null|string|Organization|Thing $memberOf ;
     * ```
     */
    const string MEMBER_OF = 'memberOf' ;

    /**
     * The role qualifying the link between the account and the subject.
     *
     * Expected values are
     * {@see \xyz\oihana\schema\enumerations\BusinessIdentityRole} constants.
     *
     * Related model property:
     *
     * ```php
     * public string|null $role ;
     * ```
     */
    const string ROLE = 'role' ;

    /**
     * The business entity the account is linked to (a person or an
     * organization).
     *
     * Related model property:
     *
     * ```php
     * public null|string|Person|Organization|Thing $subject ;
     * ```
     */
    const string SUBJECT = 'subject' ;
}
