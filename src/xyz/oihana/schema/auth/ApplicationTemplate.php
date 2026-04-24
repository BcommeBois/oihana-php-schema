<?php

namespace xyz\oihana\schema\auth;

use oihana\reflect\attributes\HydrateWith;

use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;

/**
 * Represents an ApplicationTemplate resource.
 *
 * An application template is a pre-defined configuration created by admins
 * that groups a set of scopes. Users can create M2M applications by choosing
 * one or more templates instead of selecting individual scopes.
 *
 * Access control:
 * - selfService: true → available to all authenticated users
 * - selfService: false → restricted to roles with the template assigned
 *
 * Thing provides: name, description, active, url, created, modified.
 *
 * @see Scope
 *
 * @package oihana\schema\auth
 * @author  Marc Alcaraz
 */
class ApplicationTemplate extends Thing
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The display color for this template in admin interfaces.
     * @var string|null
     */
    public string|null $color ;

    /**
     * Indicates if this template is protected (cannot be deleted via REST API).
     * @var bool|null
     */
    public bool|null $protected ;

    /**
     * The scopes grouped by this template.
     * @var array<Scope>|null
     */
    #[HydrateWith( Scope::class ) ]
    public array|null $scopes ;

    /**
     * The number of scopes attached on this template.
     * @var int|null
     */
    public int|null $scopesCount ;

    /**
     * Whether this template is available for self-service app creation.
     * @var bool|null
     */
    public bool|null $selfService ;

    /**
     * Indicates if this template is a system template (cannot be deleted or renamed via REST API).
     * @var bool|null
     */
    public bool|null $system ;
}
