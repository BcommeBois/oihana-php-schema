<?php

namespace xyz\oihana\schema\helpers\pivots;

use xyz\oihana\schema\people\Seller;

use xyz\oihana\schema\auth\User;

/**
 * Returns the seller key of an authenticated account.
 *
 * Resolves the account's first {@see Seller} business identity and returns its
 * subject `_key` — the pivot used to scope a salesperson's own resources
 * (e.g. the customers whose `assignedSeller._key` matches it).
 *
 * @param User $user The authenticated account, with its `identities` hydrated.
 *
 * @return null|int|string The seller `_key`, or `null` when the account has no
 * seller identity.
 *
 * @example
 * ```php
 * use function xyz\oihana\schema\helpers\pivots\sellerKey;
 *
 * $key = sellerKey( $currentUser ) ; // e.g. '147737218' or null
 * ```
 */
function sellerKey( User $user ) : null|int|string
{
    return $user->firstIdentityBySubjectType( Seller::getSchemaType() )?->subjectKey() ;
}
