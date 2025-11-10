<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Defines the standard keys used in Casbin policy rules.
 *
 * Casbin policies are typically represented as arrays or objects with the following structure:
 * ```
 * [
 *     'sub' => 'subject',  // the user or role performing the action
 *     'dom' => 'domain',   // the domain, tenant, or context of the resource
 *     'obj' => 'object',   // the resource being accessed
 *     'act' => 'action',   // the operation performed on the resource
 *     'eft' => 'effect',   // 'allow' or 'deny'
 * ]
 * ```
 *
 * These constants can be used to safely reference the keys in your code,
 * avoiding hardcoded strings and improving maintainability.
 *
 * @package xyz\oihana\schema
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class CasbinPolicy
{
    use ConstantsTrait ;

    /**
     * The 'act' policy key.
     * Refers to the action being performed (e.g., GET, POST, DELETE).
     */
    public const string ACTION = 'act' ;

    /**
     * The 'dom' policy key.
     * Refers to the domain, tenant, or contextual boundary of the resource.
     */
    public const string DOMAIN = 'dom' ;

    /**
     * The 'eft' policy key.
     * Refers to the effect of the policy, either 'allow' or 'deny'.
     */
    public const string EFFECT = 'eft' ;

    /**
     * The 'obj' policy key.
     * Refers to the resource being accessed (e.g., /organizations, /projects/:id).
     */
    public const string OBJECT = 'obj' ;

    /**
     * The 'sub' policy key.
     * Refers to the subject of the policy, typically a user or a role.
     */
    public const string SUBJECT = 'sub' ;

}