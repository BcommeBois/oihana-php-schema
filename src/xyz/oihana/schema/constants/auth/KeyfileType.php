<?php

namespace xyz\oihana\schema\constants\auth ;

/**
 * The supported `type` values for a Zitadel keyfile.
 *
 * Currently only `application` is exposed by Zitadel for API apps
 * (PRIVATE_KEY_JWT). Future Zitadel versions may add `serviceuser`
 * for service account keyfiles.
 *
 * @package oihana\schema\constants\auth
 * @author  Marc Alcaraz
 * @since   1.0.2
 */
enum KeyfileType : string
{
    case APPLICATION = 'application' ;
}