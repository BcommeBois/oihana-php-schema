<?php

namespace xyz\oihana\schema\auth;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\auth\RoleTrait;

/**
 * Represents a Role resource within an OAuth2 and RBAC (Role-Based Access Control) context.
 *
 * This class extends the Schema.org `WebAPI` type to describe an authorization role entity
 * that groups a set of permissions and users. It integrates seamlessly with Casbin-based
 * access control models, where roles define collections of permissions granted to specific users.
 *
 * ### Features
 * - **Permissions management** — Each role can include one or more {@see Permission} objects.
 * - **User assignment** — A role can be linked to multiple users.
 * - **Counting properties** — Provides quick access to the number of permissions or users attached.
 * - **Hydration via attributes** — Uses the `#[HydrateWith]` attribute for reflective object construction.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\auth\Role;
 * use xyz\oihana\schema\auth\Permission;
 *
 * $role = new Role();
 * $role->permissions = [
 *     new Permission(subject: 'admin', domain: 'project', action: 'read'),
 *     new Permission(subject: 'admin', domain: 'project', action: 'write'),
 * ];
 * $role->permissionsCount = count($role->permissions);
 *
 * $role->users = ['user:123', 'user:456'];
 * $role->usersCount = count($role->users);
 * ```
 *
 * ### Notes
 * - Roles can be linked to APIs or services defined by {@see WebAPI}.
 * - The `$permissions` and `$users` arrays can be hydrated automatically
 *   using reflection when deserializing from structured data.
 *
 * @see Permission
 * @see WebAPI
 * @see RoleTrait
 *
 * @package xyz\oihana\schema\auth
 * @category Security
 * @subcategory OAuth2 / RBAC
 * @since 1.0.2
 * @author Marc Alcaraz
 */
class Role extends WebAPI
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Define the permissions (scopes) that this Role uses.
     * @var array<Permission>|null
     */
    #[HydrateWith( Permission::class ) ]
    public array|null $permissions ;

    /**
     * The number of permissions attached on this Role.
     * @var int|null
     */
    public int|null $permissionsCount ;

    /**
     * Define the users that this Role is attached.
     * @var array<User>|null
     */
    #[HydrateWith( User::class ) ]
    public array|null $users ;

    /**
     * The number of users attached on this Role.
     * @var int|null
     */
    public int|null $usersCount ;

    /**
     * Returns an array of policies ready to inject dans Casbin
     */
    public function toPolicy(): array
    {
        if ( !$this->permissions )
        {
            return [];
        }

        $policies = [] ;
        foreach ( $this->permissions as $permission )
        {
            $policies[] = $permission->toPolicy() ;
        }

        return $policies;
    }
}