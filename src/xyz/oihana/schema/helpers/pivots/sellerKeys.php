<?php

namespace xyz\oihana\schema\helpers\pivots;

use xyz\oihana\schema\people\Seller;

use xyz\oihana\schema\auth\User;

/**
 * Returns every seller key of an authenticated account.
 *
 * Resolves all {@see Seller} business identities of the account and returns
 * their subject `_key` — the multi-valued pivot used to scope a salesperson's
 * own resources (e.g. the customers whose seller is one of these keys). An
 * account may wear several seller hats; this is the plural counterpart of
 * {@see sellerKey()}.
 *
 * @param User $user The authenticated account, with its `identities` hydrated.
 *
 * @return array<int,int|string> The seller `_key` list (deduplicated, never null
 * entries), or an empty array when the account has no seller identity.
 *
 * @example
 * ```php
 * use function xyz\oihana\schema\helpers\pivots\sellerKeys;
 *
 * $keys = sellerKeys( $currentUser ) ; // e.g. [ '147737218' , '147737209' ] or []
 * ```
 */
function sellerKeys( User $user ) : array
{
    $keys = [] ;

    foreach ( $user->identitiesBySubjectType( Seller::getSchemaType() ) as $identity )
    {
        $key = $identity?->subjectKey() ;

        if ( $key !== null && $key !== '' )
        {
            $keys[] = $key ;
        }
    }

    return array_values( array_unique( $keys ) ) ;
}
