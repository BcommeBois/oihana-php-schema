<?php

namespace xyz\oihana\schema\constants\traits\business;

/**
 * Defines the custom property names of the
 * {@see \xyz\oihana\schema\business\UserProfile} schema entity.
 *
 * Centralizing these keys avoids hardcoded string literals and provides a
 * single source of truth for hydration and serialization.
 *
 * > **Note** — like {@see BusinessIdentityTrait}, this trait is intentionally
 * > **not** aggregated into the global {@see \xyz\oihana\schema\constants\Oihana}
 * > constants class : its `ROLE` key collides with
 * > {@see BusinessIdentityTrait::ROLE}. It is composed directly by the
 * > {@see \xyz\oihana\schema\business\UserProfile} entity instead.
 *
 * Typical usage:
 *
 * ```php
 * $profile[ UserProfileTrait::ROLE ] = 'seller' ;
 * ```
 *
 * @package xyz\oihana\schema\constants\traits\business
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
trait UserProfileTrait
{
    /**
     * A display color for the profile (UI hint).
     *
     * Related model property:
     *
     * ```php
     * public ?string $color ;
     * ```
     */
    const string COLOR = 'color' ;

    /**
     * The expected `additionalType` of the linked person (e.g. `"Seller"`).
     *
     * Related model property:
     *
     * ```php
     * public ?string $expectedType ;
     * ```
     */
    const string EXPECTED_TYPE = 'expectedType' ;

    /**
     * The authorization role granted to accounts created from this profile.
     *
     * Related model property:
     *
     * ```php
     * public null|string|Role $role ;
     * ```
     */
    const string ROLE = 'role' ;
}