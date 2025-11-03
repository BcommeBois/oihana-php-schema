<?php

namespace xyz\oihana\schema\auth;

use org\schema\Intangible;
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
     * Examples: `'read'`, `'write'`, `'delete'`.
     *
     * @var string|null
     */
    public string|null $action = null ;

    /**
     * The domain or resource on which the action is performed.
     *
     * Examples: `'project'`, `'document'`, `'api'`.
     *
     * @var string|null
     */
    public string|null $domain = null ;

    /**
     * The subject (user or role) to whom the permission applies.
     *
     * Examples: `'admin'`, `'user:123'`.
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
            Oihana::ACTION  => $this->action  ,
        ];
    }
}