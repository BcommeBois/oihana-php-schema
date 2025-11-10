<?php

namespace xyz\oihana\schema\auth;

use org\schema\Intangible;
use xyz\oihana\schema\constants\CasbinPolicy;
use xyz\oihana\schema\constants\Effect;
use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a Casbin permission definition for Role-Based Access Control (RBAC).
 *
 * This class encapsulates the core components of a Casbin policy:
 * - `subject`: the user or role who performs the action
 * - `domain`: the resource or context where the action applies
 * - `action`: the operation allowed on the resource (e.g., 'read', 'write', 'delete')
 *
 * It provides a simple array representation to be compatible with Casbin policy enforcement.
 *
 * @package xyz\oihana\schema\auth
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class Permission extends Intangible
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The action that the permission allows.
     *
     * Examples:
     * - Basic actions : `GET`, `DELETE`, `PATCH`, `POST`, `PUT`.
     * - Multiple actions : `GET|PATCH|POST`
     *
     * @var string|null
     */
    public string|null $action = null ;

    /**
     * The domain or resource on which the action is performed.
     *
     * Examples:
     * - api      : `api.my_domain.tld`
     * - app      : 'my-app'
     * - document : `???`
     *
     * @var string|null
     */
    public string|null $domain = null ;

    /**
     * The effect of this permission : 'allow' or 'deny'.
     *
     * It determines whether the access request should be approved
     * if multiple policy rules match the request.
     * For example, one rule permits and the other denies.
     *
     * @var string|null
     */
    public string|null $effect = Effect::ALLOW ;

    /**
     * The accessed resource definition of the permission.
     *
     * Examples: `/organizations`, `/organizations/:id`
     *
     * @var string|null
     */
    public string|null $object = null ;

    /**
     * The subject (permission or user or role) to whom the permission applies.
     *
     * Examples:
     * - permission : `perm:organizations:read`
     * - role       : `role:superadmin`
     * - user       : `user:123`
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
            CasbinPolicy::SUBJECT => $this->subject,
            CasbinPolicy::DOMAIN => $this->domain,
            CasbinPolicy::OBJECT => $this->object,
            CasbinPolicy::ACTION => $this->action,
            CasbinPolicy::EFFECT => $this->effect,
        ];
    }
}