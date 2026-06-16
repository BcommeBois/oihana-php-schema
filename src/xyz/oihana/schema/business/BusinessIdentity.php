<?php

namespace xyz\oihana\schema\business;

use org\schema\Intangible;
use org\schema\Organization;
use org\schema\Person;
use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\BusinessIdentityTrait;

/**
 * Links an authenticated account to a business entity.
 *
 * A *business identity* is the typed association between an account (the
 * principal that authenticates) and the business entity it corresponds to —
 * a {@see Person} (e.g. a seller, a customer contact) or an
 * {@see Organization} (e.g. a customer). It answers the question *"who is this
 * account, in business terms?"* without ever merging the account's own data
 * with the linked entity's data.
 *
 * ### Why a dedicated entity
 * The account record and the business entity live in distinct systems and
 * **must not be fused** : the account `givenName` may legitimately differ from
 * the linked person's `givenName`, and the business source may be read-only.
 * `BusinessIdentity` keeps the link explicit and side-effect free : the
 * {@see BusinessIdentity::$subject} (and optional {@see BusinessIdentity::$memberOf})
 * are resolved references, not copies.
 *
 * ### Cardinality
 * An account may hold several identities (e.g. both a seller and a customer
 * contact), so consumers typically expose them as a list. The role of each
 * link is carried by {@see BusinessIdentity::$role} (see
 * {@see \xyz\oihana\schema\enumerations\BusinessIdentityRole}).
 *
 * Intentionally extends {@see Intangible} rather than {@see Thing} : an
 * identity is a qualifier of an account, not an independently addressable
 * resource. It still inherits the ArangoDB metadata (`_from`/`_to`),
 * `created`/`modified`, `active` and `identifier` from {@see Thing}.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\business\BusinessIdentity;
 * use xyz\oihana\schema\enumerations\BusinessIdentityRole;
 *
 * // A seller account
 * $identity = new BusinessIdentity
 * ([
 *     BusinessIdentity::ROLE    => BusinessIdentityRole::SELLER ,
 *     BusinessIdentity::SUBJECT => [ '@type' => 'Person' , 'id' => 'BECOU' , 'givenName' => 'Benjamin' ] ,
 * ]);
 *
 * // A customer contact account (person acting for an organization)
 * $identity = new BusinessIdentity
 * ([
 *     BusinessIdentity::ROLE      => BusinessIdentityRole::CUSTOMER_CONTACT ,
 *     BusinessIdentity::SUBJECT   => [ '@type' => 'Person'       , 'id' => '94565'  ] ,
 *     BusinessIdentity::MEMBER_OF => [ '@type' => 'Organization' , 'id' => '13658'  ] ,
 * ]);
 * ```
 *
 * @see \xyz\oihana\schema\enumerations\BusinessIdentityRole
 * @see BusinessIdentityTrait
 *
 * @package xyz\oihana\schema\business
 * @category Business
 * @author  Marc Alcaraz (ekameleon)
 */
class BusinessIdentity extends Intangible
{
    use BusinessIdentityTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The organization the {@see BusinessIdentity::$subject} belongs to.
     *
     * Optional — typically set when the subject is a person acting for an
     * organization (e.g. a customer contact linked to its customer
     * organization). A resolved reference, never a copy.
     *
     * @var null|string|Organization|Thing
     */
    public null|string|Organization|Thing $memberOf ;

    /**
     * The role qualifying the link between the account and the subject.
     *
     * Expected values are
     * {@see \xyz\oihana\schema\enumerations\BusinessIdentityRole} constants
     * (`customer`, `customerContact`, `seller`, `provider`, `deliverer`).
     * Stored as a string for forward-compatibility with future roles.
     *
     * @var string|null
     */
    public string|null $role ;

    /**
     * The business entity the account is linked to.
     *
     * A {@see Person} (e.g. a seller or a customer contact) or an
     * {@see Organization} (e.g. a customer). A resolved reference, never a
     * copy of the account's own data.
     *
     * @var null|string|Person|Organization|Thing
     */
    public null|string|Person|Organization|Thing $subject ;

    /**
     * Extracts an identifier from a resolved reference.
     *
     * A {@see Thing} reference is probed for its `id`, then `_key`, then `_id`
     * (all tolerant when undefined). A scalar reference (or `null`) is returned
     * as-is.
     *
     * @param null|string|Thing $value The reference to extract the id from.
     *
     * @return null|int|string The resolved id, or `null`.
     */
    private static function extractId( null|string|Thing $value ) : null|int|string
    {
        if ( $value instanceof Thing )
        {
            return $value->id ?? $value->_key ?? $value->_id ?? null ;
        }

        return $value ;
    }

    /**
     * Indicates whether this identity carries the given role.
     *
     * Tolerant when {@see BusinessIdentity::$role} is not defined : returns
     * `false` rather than raising an error.
     *
     * @param string $role The role to test against (typically a
     * {@see \xyz\oihana\schema\enumerations\BusinessIdentityRole} constant).
     *
     * @return bool `true` if the role is defined and strictly equals `$role`.
     */
    public function is( string $role ) : bool
    {
        return isset( $this->role ) && $this->role === $role ;
    }

    /**
     * Returns the identifier of the {@see BusinessIdentity::$memberOf}.
     *
     * Resolves the id whether the member is a scalar reference or a
     * {@see Thing} (probed for `id`, then `_key`, then `_id`).
     *
     * @return null|int|string The member id, or `null` if it cannot be resolved.
     */
    public function memberOfId() : null|int|string
    {
        return self::extractId( $this->memberOf ?? null ) ;
    }

    /**
     * Returns the identifier of the {@see BusinessIdentity::$subject}.
     *
     * Resolves the id whether the subject is a scalar reference or a
     * {@see Thing} (probed for `id`, then `_key`, then `_id`).
     *
     * @return null|int|string The subject id, or `null` if it cannot be resolved.
     */
    public function subjectId() : null|int|string
    {
        return self::extractId( $this->subject ?? null ) ;
    }
}
