<?php

namespace xyz\oihana\schema\business;

use org\schema\Intangible;
use org\schema\Organization;
use org\schema\Person;
use org\schema\Thing;

use org\schema\constants\Schema;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\BusinessIdentityTrait;

use function oihana\core\accessors\getKeyValue;
use function oihana\core\arrays\toArray;

/**
 * Links an authenticated account to a business entity.
 *
 * A *business identity* associates an account (the principal that authenticates)
 * with the business entity it corresponds to — typically a {@see Person} or an
 * {@see Organization}, exposed through {@see BusinessIdentity::$subject}. It
 * answers *"who is this account, in business terms?"* without merging the
 * account's own data with the linked entity's : the subject is a resolved
 * reference, never a copy.
 *
 * An account may hold several identities, so consumers usually expose them as a
 * list (see {@see \xyz\oihana\schema\auth\User::$identities}).
 *
 * Intentionally extends {@see Intangible} rather than {@see Thing} : an identity
 * qualifies an account, it is not an independently addressable resource. It
 * still inherits the ArangoDB metadata (`_from`/`_to`), `created`/`modified`,
 * `active` and `identifier` from {@see Thing}.
 *
 * ### Example
 * ```php
 * use org\schema\Person;
 * use xyz\oihana\schema\business\BusinessIdentity;
 *
 * $identity = new BusinessIdentity
 * ([
 *     BusinessIdentity::SUBJECT => new Person([ '_key' => '94565' , 'additionalType' => 'Seller' ]) ,
 * ]);
 *
 * $identity->subjectKey() ;          // '94565'
 * $identity->isType( 'Seller' ) ;    // true
 * ```
 *
 * @see BusinessIdentityTrait
 *
 * @package xyz\oihana\schema\business
 * @category Business
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class BusinessIdentity extends Intangible
{
    use BusinessIdentityTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The business entity the account is linked to.
     * A {@see Person} or an {@see Organization}. A resolved reference, never a copy of the account's own data.
     * @var null|string|array|Person|Organization|Thing
     */
    public null|string|array|Person|Organization|Thing $subject ;

    /**
     * Indicates whether the {@see BusinessIdentity::$subject} carries the given type.
     *
     * Compares the subject's Schema.org `additionalType` against `$type` : strict
     * equality, or membership when `additionalType` is an array.
     *
     * @param string $type The type to test against.
     *
     * @return bool `true` if the subject type is defined and matches `$type`.
     */
    public function isType( string $type ) : bool
    {
        $subjectType = $this->subjectType() ;
        return is_array( $subjectType )
            ? in_array( $type , $subjectType , true )
            : $subjectType === $type ;
    }

    /**
     * Returns an identifier of the {@see BusinessIdentity::$subject}.
     *
     * Probes the given key (or ordered list of keys) on the resolved subject
     * reference ; a scalar subject is returned as-is. The choice of key(s) is
     * left to the caller.
     *
     * @param string|array $key The key, or ordered list of keys, to probe. Default {@see Schema::_KEY}.
     *
     * @return null|int|string The resolved identifier, or `null`.
     */
    public function subjectKey( string|array $key = Schema::_KEY ) : null|int|string
    {
        return $this->extractKey( $this->subject ?? null , $key ) ;
    }

    /**
     * Returns the Schema.org `additionalType` of the {@see BusinessIdentity::$subject}.
     *
     * Reads the `additionalType` whether the subject is an object or an
     * associative array (a raw projection reference). Tolerant : returns `null`
     * when the subject is a scalar reference, `null`, or carries no
     * `additionalType`.
     *
     * @return array|string|null The subject `additionalType`, or `null`.
     */
    public function subjectType() : array|string|null
    {
        $subject = $this->subject ?? null ;

        if ( is_array( $subject ) || is_object( $subject ) )
        {
            $type = getKeyValue( $subject , Schema::ADDITIONAL_TYPE ) ;

            return ( is_array( $type ) || is_string( $type ) ) ? $type : null ;
        }

        return null ;
    }

    /**
     * Returns an identifier of the organization referenced by the subject's
     * Schema.org `worksFor` property.
     *
     * Reads the subject's `worksFor` whether the subject is an object or an
     * associative array, then probes the given key (or ordered list of keys) on
     * it ; a scalar reference is returned as-is. Returns `null` when the subject
     * has no `worksFor`.
     *
     * @param string|array $key The key, or ordered list of keys, to probe. Default {@see Schema::_KEY}.
     *
     * @return null|int|string The resolved identifier, or `null`.
     */
    public function worksForKey( string|array $key = Schema::_KEY ) : null|int|string
    {
        $subject  = $this->subject ?? null ;
        $worksFor = ( is_array( $subject ) || is_object( $subject ) ) ? getKeyValue( $subject , Schema::WORKS_FOR ) : null ;

        return $this->extractKey( $worksFor , $key ) ;
    }

    /**
     * Extracts an identifier from a resolved reference, trying the given key(s) in order.
     *
     * A scalar reference (or `null`) is returned as-is — it *is* the identifier.
     * For an object or an associative array, each candidate key is probed in
     * order (via {@see getKeyValue}) and the first
     * non-null scalar wins.
     *
     * @param mixed        $value The reference to extract from (object, array, scalar or null).
     * @param string|array $key   The key, or ordered list of keys, to probe. Default {@see Schema::_KEY}.
     *
     * @return null|int|string The resolved identifier, or `null`.
     */
    private function extractKey( mixed $value , string|array $key = Schema::_KEY ) : null|int|string
    {
        if ( $value === null )
        {
            return null ;
        }

        if ( !is_array( $value ) && !is_object( $value ) )
        {
            return is_scalar( $value ) ? $value : null ;
        }

        foreach ( toArray( $key ) as $candidate )
        {
            $resolved = getKeyValue( $value , $candidate ) ;

            if ( is_int( $resolved ) || is_string( $resolved ) )
            {
                return $resolved ;
            }
        }

        return null ;
    }
}
