<?php

namespace xyz\oihana\schema\helpers\pivots;

use xyz\oihana\schema\people\CustomerEmployee;

use xyz\oihana\schema\auth\User;

/**
 * Returns the customer organization key an authenticated account is a contact for.
 *
 * Resolves the account's first {@see CustomerEmployee} business identity and
 * returns the `_key` of the organization it works for (`subject.worksFor`) — the
 * pivot used to scope a customer contact's resources (e.g. invoices or pricing
 * conditions of that customer). A contact works for a single customer, so a
 * single key is returned.
 *
 * @param User $user The authenticated account, with its `identities` hydrated.
 *
 * @return null|int|string The customer organization `_key`, or `null` when the
 * account has no customer-contact identity.
 *
 * @example
 * ```php
 * use function xyz\oihana\schema\helpers\pivots\customerKey;
 *
 * $key = customerKey( $currentUser ) ; // e.g. '137285125' or null
 * ```
 */
function customerKey( User $user ) : null|int|string
{
    return $user->firstIdentityBySubjectType( CustomerEmployee::getSchemaType() )?->worksForKey() ;
}
