<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Defines the supported effects for Casbin permissions or RBAC rules.
 *
 * In a policy rule, the effect determines whether a matched permission grants
 * or denies access. Typical usage in Casbin:
 * ```
 * [
 *     'sub' => 'role:admin',
 *     'dom' => 'project',
 *     'obj' => '/documents/:id',
 *     'act' => 'GET|POST',
 *     'eft' => Effect::ALLOW
 * ]
 * ```
 *
 * **Effect values:**
 * - `allow` — Grants access to the requested resource/action.
 * - `deny`  — Explicitly denies access, even if another rule allows it.
 *
 * Using these constants avoids hardcoding strings and ensures consistency
 * across your RBAC or Casbin implementations.
 *
 * @package xyz\oihana\schema
 * @category Security / RBAC
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class Effect
{
    use ConstantsTrait ;

    /**
     * Grants access for the permission or policy.
     */
    public const string ALLOW = 'allow' ;

    /**
     * Denies access for the permission or policy.
     */
    public const string DENY = 'deny' ;

}