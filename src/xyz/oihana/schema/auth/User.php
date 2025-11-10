<?php

namespace xyz\oihana\schema\auth;

use oihana\reflect\attributes\HydrateWith;

use org\schema\Person;

use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a User resource within an OAuth2 and RBAC (Role-Based Access Control) context.
 *
 * @package xyz\oihana\schema\auth
 * @category Security
 * @subcategory OAuth2 / RBAC
 * @since 1.0.2
 * @author Marc Alcaraz
 */
class User extends Person
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Data that the user has read-only access to (e.g. roles, permissions, vip, etc)
     * @var array|object|null
     */
    public array|object|null $appMetadata ;

    /**
     * The authorized applications of this particular user.
     * @var array|null
     */
    public array|null $applications ;

    /**
     * Indicates if the User is blocked for specific API, Applications, etc.
     * @var array|string|null
     */
    public array|null|string $blockedFor ;

    /**
     * The devices being used by this particular user.
     * This list is used to unlink the User, the refresh token will be revoked, forcing the user to re-login on the application.
     * @var array|null
     */
    public array|null $devices ;

    /**
     * Date of the latest login.
     * @var string|null
     */
    public string|null $lastLogin ;

    /**
     * The numbers of logins of the User.
     * @var string|null
     */
    public string|null $loginsCount ;

    /**
     * Data that the user has read/write access to (e.g. color_preference, blog_url, etc.)
     * @var array|object|null
     */
    public array|object|null $metadata ;

    /**
     * Define the permissions (scopes) that this particular User.
     * @var array<Permission>|null
     */
    #[HydrateWith( Permission::class ) ]
    public array|null $permissions ;

    /**
     * The number of permissions attached on this particular User.
     * @var int|null
     */
    public int|null $permissionsCount ;

    /**
     * Define the roles (scopes) that this particular User.
     * @var array<Role>|null
     */
    #[HydrateWith( Role::class ) ]
    public array|null $roles ;

    /**
     * The number of roles attached on this particular User.
     * @var int|null
     */
    public int|null $rolesCount ;

    /**
     * Date of the signed-up of the User.
     * @var string|null
     */
    public string|null $signedUp ;
}