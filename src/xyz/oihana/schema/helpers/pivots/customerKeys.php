<?php

namespace xyz\oihana\schema\helpers\pivots;

use xyz\oihana\schema\people\CustomerEmployee;

use xyz\oihana\schema\auth\User;

/**
 * Returns every customer organization key an authenticated account is a contact for.
 *
 * Resolves all {@see CustomerEmployee} business identities of the account and
 * returns, for each, the `_key` of the organization it works for
 * (`subject.worksFor`) — the multi-valued pivot used to scope the resources of
 * the customer(s) an account represents (e.g. the products priced for them). An
 * account may be the contact of several customer organizations (e.g. the manager
 * of two companies); this is the plural counterpart of {@see customerKey()}.
 *
 * @param User $user The authenticated account, with its `identities` hydrated.
 *
 * @return array<int,int|string> The customer organization `_key` list
 * (deduplicated, never null entries), or an empty array when the account has no
 * customer-contact identity.
 *
 * @example
 * ```php
 * use function xyz\oihana\schema\helpers\pivots\customerKeys;
 *
 * $keys = customerKeys( $currentUser ) ; // e.g. [ '137285125' , '137285130' ] or []
 * ```
 */
function customerKeys( User $user ) : array
{
    $keys = [] ;

    foreach ( $user->identitiesBySubjectType( CustomerEmployee::getSchemaType() ) as $identity )
    {
        $key = $identity?->worksForKey() ;

        if ( $key !== null && $key !== '' )
        {
            $keys[] = $key ;
        }
    }

    return array_values( array_unique( $keys ) ) ;
}
