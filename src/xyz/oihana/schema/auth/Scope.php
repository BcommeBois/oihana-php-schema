<?php

namespace xyz\oihana\schema\auth;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a Scope resource within an OAuth2 and RBAC context.
 *
 * A scope groups a set of permissions that can be assigned to applications (M2M).
 * Scopes are to applications what roles are to users.
 *
 * ### Features
 * - **Permissions management** — Each scope can include one or more {@see Permission} objects.
 * - **Counting properties** — Provides quick access to the number of permissions attached.
 * - **Hydration via attributes** — Uses the `#[HydrateWith]` attribute for reflective object construction.
 *
 * @see Permission
 * @see WebAPI
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 */
class Scope extends WebAPI
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The display color for this scope in admin interfaces.
     * @var string|null
     */
    public string|null $color ;

    /**
     * Define the permissions that this Scope groups.
     * @var array<Permission>|null
     */
    #[HydrateWith( Permission::class ) ]
    public array|null $permissions ;

    /**
     * The number of permissions attached on this Scope.
     * @var int|null
     */
    public int|null $permissionsCount ;

    /**
     * Indicates if this scope is protected (cannot be deleted via REST API).
     * @var bool|null
     */
    public bool|null $protected ;

    /**
     * Indicates if this scope is a system scope (cannot be deleted or renamed via REST API).
     * @var bool|null
     */
    public bool|null $system ;
}
