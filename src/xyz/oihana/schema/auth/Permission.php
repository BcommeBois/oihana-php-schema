<?php

namespace xyz\oihana\schema\auth;

use org\schema\Intangible;
use xyz\oihana\schema\constants\CasbinPolicy;
use xyz\oihana\schema\constants\Effect;
use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a Casbin permission rule for Role-Based Access Control (RBAC).
 *
 * A `Permission` defines what a subject (user, role, or permission)
 * is allowed or denied to do on a given resource within a specific domain.
 * It is directly compatible with Casbin policy enforcement.
 *
 * ### Components
 * - **subject**: The entity performing the action (`user:123`, `role:admin`, `perm:read:org`)
 * - **domain**: The context or namespace for the permission (`api.my_domain.tld`, `my-app`)
 * - **object**: The resource being accessed (`/organizations`, `/documents/:id`)
 * - **action**: The allowed operations (`GET`, `POST`, `PATCH|PUT`, etc.)
 * - **effect**: Whether access is granted or denied (`allow` or `deny`)
 *
 * ### Usage Example
 * ```php
 * use xyz\oihana\schema\auth\Permission;
 * use xyz\oihana\schema\constants\Effect;
 *
 * $perm = new Permission
 * ([
 *     'subject' => 'role:admin' ,
 *     'domain'  => 'api.my-domain.tld' ,
 *     'object'  => '/documents/:id' ,
 *     'action'  => 'GET|POST' ,
 *     'effect'  => Effect::ALLOW ,
 * ]);
 * ```
 *
 * ### Notes
 * - Multiple actions can be specified using the `|` character.
 * - For REST APIs, consider normalizing objects with wildcards
 *   (e.g., `/documents/*`) so that `keyMatch2` in Casbin matches correctly.
 * - The effect is used to resolve conflicts when multiple rules match:
 *   an `allow` can be overridden by a `deny` depending on your `policy_effect`.
 *
 * @package xyz\oihana\schema\auth
 * @category Security / RBAC
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
class Permission extends Intangible
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The allowed action(s) for this permission.
     *
     * Examples:
     * - Basic actions : `GET`
     * - Multiple actions : `GET|PATCH|POST`
     *
     * @var string|null
     */
    public string|null $action = null ;

    /**
     * The domain or namespace where this permission applies.
     *
     * Examples:
     * - API         : `api.my_domain.tld`
     * - Application : 'my-app'
     *
     * @var string|null
     */
    public string|null $domain = null ;

    /**
     * The effect of this permission : 'allow' or 'deny'.
     *
     * Used to determine whether the access request is approved
     * when multiple policy rules match.
     *
     * @var string
     */
    public string $effect
    {
        get => $this->_effect ;
        set
        {
            $this->_effect = $value == Effect::DENY ? Effect::DENY : Effect::ALLOW ;
        }
    }

    /**
     * The resource object targeted by this permission.
     *
     * Examples:
     * - `/organizations`
     * - `/documents/:id`
     *
     * @var string|null
     */
    public string|null $object = null ;

    /**
     * The subject (permission or user or role) to whom the permission applies.
     *
     * Examples:
     * - Permission : `perm:organizations:read`
     * - Role       : `role:superadmin`
     * - User       : `user:123`
     *
     * @var string|null
     */
    public string|null $subject = null ;

    /**
     * Returns an array representation of the permission suitable for Casbin policies.
     *
     * @return array{subject: string|null, domain: string|null, action: string|null}
     */
    public function toArray(): array
    {
        return
        [
            Oihana::SUBJECT => $this->subject ,
            Oihana::DOMAIN  => $this->domain  ,
            Oihana::OBJECT  => $this->object  ,
            Oihana::ACTION  => $this->action  ,
            Oihana::EFFECT  => $this->effect  ,
        ];
    }

    /**
     * Returns an array ready for Casbin policies: [sub, dom, obj, act, eft]
     */
    public function toPolicy(): array
    {
        return
        [
            CasbinPolicy::SUBJECT => $this->subject ,
            CasbinPolicy::DOMAIN  => $this->domain  ,
            CasbinPolicy::OBJECT  => $this->object  ,
            CasbinPolicy::ACTION  => $this->action  ,
            CasbinPolicy::EFFECT  => $this->effect  ,
        ];
    }

    /**
     * The effect of this permission: always 'allow' or 'deny'.
     */
    private string $_effect = Effect::ALLOW ;
}