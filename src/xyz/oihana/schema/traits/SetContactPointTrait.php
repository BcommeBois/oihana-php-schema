<?php

namespace xyz\oihana\schema\traits ;

use org\schema\constants\Schema;
use org\schema\ContactPoint;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\ContactType;

/**
 * Injects the contact points of an entity from flat property expressions.
 *
 * The consumer class must declare a `contactPoint` property. Each flat key, e.g.
 * `default_telephone`, `mobile` or `home_email`, is routed to the {@see \org\schema\ContactPoint}
 * of the matching {@see \xyz\oihana\schema\enumerations\ContactType}, created or updated on demand.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\traits
 * @since   1.3.0
 */
trait SetContactPointTrait
{
    /**
     * Internal method to inject custom properties in the PostalAddress property of the place.
     *
     * @param string $name  The name of the property to transform.
     * @param mixed  $value The value of the property.
     *
     * @return bool
     */
    protected function setContactPointProperty( string $name , mixed $value ):bool
    {
        if( !empty( $value ) )
        {
            $type = match( $name )
            {
                Oihana::DEFAULT_EMAIL , Oihana::DEFAULT_FAX_NUMBER , Oihana::DEFAULT_TELEPHONE  => ContactType::DEFAULT ,
                Oihana::HOME_EMAIL    , Oihana::HOME_FAX_NUMBER    , Oihana::HOME_TELEPHONE     => ContactType::HOME ,

                Oihana::MOBILE              => ContactType::MOBILE  ,
                Oihana::MOBILE_PROFESSIONAL => ContactType::MOBILE_PROFESSIONAL ,

                default => null
            };

            if( empty( $type ) )
            {
                return false ;
            }

            $contactPoint = $this->contactPointList() ;

            $foundKey = null ;
            foreach ( $contactPoint as $key => $existingContact )
            {
                $existingType = $existingContact->contactType ?? null ;
                if ( $existingType === $type )
                {
                    $foundKey = $key;
                    break;
                }
            }

            $contact = $foundKey !== null ? $contactPoint[$foundKey] : new ContactPoint() ;

            $hasValidData = false ;

            if(
                ( $name == Oihana::MOBILE || $name == Oihana::MOBILE_PROFESSIONAL || str_ends_with( $name , Schema::TELEPHONE ) )
               && $this->isValidPhoneNumber( $value )
            )
            {
                $contact->telephone = $value ;
                // $contact->telephone = PhoneNumberUtil::getInstance()->format( $value , PhoneNumberFormat::INTERNATIONAL) ;
                $hasValidData = true ;
            }
            else if( str_ends_with( $name , Schema::EMAIL ) && filter_var( $value , FILTER_VALIDATE_EMAIL ) )
            {
                $contact->email = $value ;
                $hasValidData = true ;
            }
            else if( str_ends_with( $name , Schema::FAX_NUMBER ) )
            {
                $contact->faxNumber = $value ;
                $hasValidData = true ;
            }

            if( $hasValidData )
            {
                $contact->contactType = $type ;

                if ( $foundKey !== null )
                {
                    $contactPoint[ $foundKey ] = $contact ;
                }
                else
                {
                    $contactPoint[] = $contact ;
                }

                $this->contactPoint = $contactPoint;

                return true ;
            }
        }

        return false ;
    }

    /**
     * Find a specific ContactPoint reference by type.
     *
     * Entries that are not hydrated {@see ContactPoint} instances — the raw
     * arrays a base read hands back, or an unresolved reference — are skipped
     * rather than dereferenced, so only a real instance is ever returned.
     *
     * @param  ?string $type The type of the resource to find.
     *
     * @return ContactPoint|null
     */
    protected function findContactPointByType( ?string $type ) :?ContactPoint
    {
        $found = array_find
        (
            $this->contactPointList() ,
            fn( mixed $contact ) => $contact instanceof ContactPoint && $contact->contactType === $type
        );

        return $found instanceof ContactPoint ? $found : null ;
    }

    /**
     * The contact points as a list, whatever shape the property holds.
     *
     * `contactPoint` is declared as `null|ContactPoint|array|string` wherever
     * this trait is composed, but the code around it only ever makes sense on a
     * list. A lone {@see ContactPoint} and an unresolved string reference are
     * therefore wrapped rather than discarded — nothing is lost, and the caller
     * can iterate and append without checking first.
     *
     * @return array The contact points, always a list.
     */
    protected function contactPointList() :array
    {
        $contactPoint = $this->contactPoint ?? null ;

        return match( true )
        {
            is_array( $contactPoint ) => $contactPoint ,
            $contactPoint === null    => [] ,
            default                   => [ $contactPoint ] ,
        };
    }

    /**
     * Validate a phone number expression (basic check, any country).
     *
     * @param string      $phone The phone number to validate.
     * @param string|null $defaultRegion region that we are expecting the number to be from. This is only used
     *                                   if the number being parsed is not written in international format.
     *                                   The country_code for the number in this case would be stored as that
     *                                   of the default region supplied. If the number is guaranteed to
     *                                   start with a '+' followed by the country calling code, then
     *                                   "ZZ" or null can be supplied.
     *
     * @return bool True if valid, false otherwise.
     */
    protected function isValidPhoneNumber( string $phone , ?string $defaultRegion = 'FR' ): bool
    {
        if ( empty( $phone ) || $phone == '.' )
        {
            return false ;
        }

        return true ;
    }
}