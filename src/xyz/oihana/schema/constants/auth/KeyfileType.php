<?php

namespace xyz\oihana\schema\constants\auth ;

use oihana\reflect\traits\ConstantsTrait;

/**
 * The supported `type` values emitted in a keyfile by the IdP.
 *
 * - `APPLICATION` — historical API app keyfile (PRIVATE_KEY_JWT,
 *   `Zitadel /apps/{id}/keys`). Kept for completeness.
 * - `SERVICE_ACCOUNT` — modern service-user keyfile (machine user
 *   key, `Zitadel /users/{id}/keys`). The current target for M2M
 *   clients.
 *
 * @package oihana\schema\constants\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
class KeyfileType
{
    use ConstantsTrait;

    /**
     * The "application" keyfile type.
     */
    public const string APPLICATION = 'application'    ;

    /**
     * The "service account" keyfile type.
     */
    public const string SERVICE_ACCOUNT = 'serviceaccount' ;
}