<?php

namespace xyz\oihana\schema\auth;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a Policy resource within an RBAC context for M2M applications.
 *
 * A Policy groups a set of permissions that can be assigned to applications (M2M)
 * or attached to roles (to gate self-service application creation).
 *
 * Equivalent to AWS IAM Managed Policies, Google IAM Predefined/Custom Roles
 * (when assigned to Service Accounts), or Auth0 Permission Sets.
 *
 * @see Permission
 * @see Application
 * @see Role
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
class Policy extends WebAPI
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The applications that reference this policy (inbound).
     * @var array<Application>|null
     */
    #[HydrateWith( Application::class ) ]
    public array|null $applications ;

    /**
     * The number of applications referencing this policy.
     * @var int|null
     */
    public int|null $applicationsCount ;

    /**
     * The display color for this policy in admin interfaces.
     * @var string|null
     */
    public string|null $color ;

    /**
     * Define the permissions that this Policy groups.
     * @var array<Permission>|null
     */
    #[HydrateWith( Permission::class ) ]
    public array|null $permissions ;

    /**
     * The number of permissions attached on this Policy.
     * @var int|null
     */
    public int|null $permissionsCount ;

    /**
     * Indicates if this policy is protected (cannot be deleted via REST API).
     * @var bool|null
     */
    public bool|null $protected ;

    /**
     * Reverse: roles that grant this policy to their users.
     * @var array<Role>|null
     */
    #[HydrateWith( Role::class ) ]
    public array|null $roles ;

    /**
     * The number of roles referencing this policy.
     * @var int|null
     */
    public int|null $rolesCount ;

    /**
     * Indicates if this policy is a system policy (cannot be deleted or renamed via REST API).
     * @var bool|null
     */
    public bool|null $system ;
}
