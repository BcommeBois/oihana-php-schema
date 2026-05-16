<?php

namespace xyz\oihana\schema\auth;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\auth\PolicyTrait;

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
    use PolicyTrait ;

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
     * The services that reference this policy (inbound).
     * @var array<Service>|null
     */
    #[HydrateWith( Service::class ) ]
    public array|null $services ;

    /**
     * The number of services referencing this policy.
     * @var int|null
     */
    public int|null $servicesCount ;

    /**
     * Returns an array of Casbin-ready policy entries built from the
     * permissions attached to this Policy. Returns an empty array when
     * no permissions are attached.
     */
    public function toPolicy(): array
    {
        if ( empty( $this->permissions ?? null ) )
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

    /**
     * Alias of {@see Policy::toPolicy()} with an explicit Casbin-oriented name.
     */
    public function toCasbinPolicy(): array
    {
        return $this->toPolicy();
    }
}
