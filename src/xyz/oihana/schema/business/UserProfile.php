<?php

namespace xyz\oihana\schema\business;

use org\schema\Intangible;

use xyz\oihana\schema\auth\Role;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\UserProfileTrait;

/**
 * A reusable template for provisioning a user account.
 *
 * A *user profile* is an administrator-managed blueprint that pairs an
 * authorization {@see Role} with the kind of business entity the account is
 * expected to be linked to. It is **not** a property of a user : it is a
 * convenience consumed once, at creation time, to wire an account in a single
 * step — grant its role (the auth axis) and attach its business identity (the
 * identity axis, see {@see BusinessIdentity}).
 *
 * ### What it is (and is not)
 * - It carries **no per-account state**. Once a user is created from a profile,
 *   the user owns its roles and its identities directly ; the profile is a
 *   creation-time gabarit, never the runtime source of truth for permissions
 *   or routing.
 * - {@see UserProfile::$role} is the authorization role to grant.
 * - {@see UserProfile::$expectedType} is the `additionalType` the linked person
 *   must match (e.g. `"Seller"`, `"CustomerEmployee"`), so a server can validate
 *   the owner supplied at creation.
 *
 * Extends {@see Intangible} : a profile is a definition, not an independently
 * addressable resource. It inherits `name`, `description`, `url`,
 * `created`/`modified` and the ArangoDB metadata (`_key`) from
 * {@see \org\schema\Thing}.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\business\UserProfile;
 *
 * $profile = new UserProfile
 * ([
 *     'name'                     => 'Commercial' ,
 *     UserProfile::COLOR         => '#22C55E' ,
 *     UserProfile::ROLE          => 'seller' , // an auth Role reference
 *     UserProfile::EXPECTED_TYPE => 'Seller' , // expected Person additionalType
 * ]);
 * ```
 *
 * @see Role
 * @see BusinessIdentity
 * @see UserProfileTrait
 *
 * @package xyz\oihana\schema\business
 * @category Business
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class UserProfile extends Intangible
{
    use UserProfileTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * A display color for the profile (UI hint), e.g. `#22C55E`.
     *
     * @var string|null
     */
    public ?string $color ;

    /**
     * The expected `additionalType` of the person the account will be linked
     * to, e.g. `"Seller"` or `"CustomerEmployee"`.
     *
     * Used at creation time to validate that the supplied owner (a person) is of the type this profile provisions.
     *
     * @var string|null
     */
    public ?string $expectedType ;

    /**
     * The authorization role granted to accounts created from this profile.
     *
     * A resolved reference — either a role key / name (string) or a hydrated {@see Role} object.
     *
     * @var null|string|Role
     */
    public null|string|Role $role ;
}