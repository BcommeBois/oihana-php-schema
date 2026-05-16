<?php

namespace xyz\oihana\schema\auth\traits;

/**
 * Provides common authorization and protection-related properties for a resource.
 *
 * This trait can be used by schema objects, entities, or models representing
 * protected resources such as roles, permissions, groups, or system-managed
 * objects in an authentication or authorization context.
 *
 * The properties exposed by this trait are typically used to control:
 *
 * - Visual representation (`$color`)
 * - Write protection through APIs (`$protected`)
 * - System-level immutability (`$system`)
 *
 * @package xyz\oihana\schema\auth\traits
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
trait HasProtectedResource
{
    /**
     * The display color associated with this resource.
     *
     * This value is generally used for UI rendering purposes such as labels,
     * badges, tags, or administration panels.
     *
     * Example:
     * ```php
     * $resource->color = '#ff6600';
     * ```
     *
     * @var string|null
     */
    public string|null $color ;

    /**
     * Indicates whether this resource is protected.
     *
     * A protected resource cannot normally be modified or assigned through
     * public REST API operations or non-privileged actions.
     *
     * Example:
     * ```php
     * $resource->protected = true;
     * ```
     *
     * @var bool|null
     */
    public bool|null $protected ;

    /**
     * Indicates whether this resource is a system-defined resource.
     *
     * System resources are generally immutable and cannot be deleted through
     * REST APIs or standard administrative operations.
     *
     * Example:
     * ```php
     * $resource->system = true;
     * ```
     *
     * @var bool|null
     */
    public bool|null $system ;
}