<?php

namespace xyz\oihana\schema\helpers\hydrate\appointments;

use ReflectionException;

use xyz\oihana\schema\enumerations\AppointmentCancelled;
use xyz\oihana\schema\enumerations\AppointmentDone;
use xyz\oihana\schema\enumerations\AppointmentNoShow;
use xyz\oihana\schema\enumerations\AppointmentPlanned;
use xyz\oihana\schema\enumerations\AppointmentStatus;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\findEnumerationMember;

/**
 * Hydrate an array definition with the member class of {@see AppointmentStatus} it names.
 *
 * The twin of {@see \org\schema\helpers\hydrate\hydrateEventStatus()}, on the other axis :
 * that one says what became of the *slot*, this one what became of the *meeting*. Both are
 * read back the same way, so a consumer holding a stored appointment does not have to
 * treat them differently.
 *
 * A status is written one of two ways : the bare constant, when there is nothing more to
 * say, or the member class, when there is — `new AppointmentNoShow([ 'description' => '…' ])`
 * says what was found on the doorstep, and a string cannot.
 *
 * 🔑 **The bare constant comes back untouched.** It is not an array, so it falls through
 * the first guard : whichever form was stored can be handed to this helper.
 *
 * The member is resolved by {@see findEnumerationMember()}, from the payload's
 * `additionalType` — the URI every member states of itself — then from its `@type`, which
 * carries the short name.
 *
 * ⚠️ **The two never coincide here**, unlike on the Schema.org side : this vocabulary
 * spells its members `…/AppointmentStatus#NoShow` while the class is named
 * `AppointmentNoShow`, so neither key can be derived from the other — which is precisely
 * why the URI is stated by the member rather than read off its class name.
 *
 * Nothing recognized gives an {@see AppointmentStatus} : the status is unknown to this
 * vocabulary, and answering `null` would throw away the reason it carried.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed $init Single status data, a list of such data, or any other value.
 *
 * @return mixed
 *
 * @throws ReflectionException
 *
 * @example
 * ```php
 * hydrateAppointmentStatus( AppointmentStatus::DONE ) ;                        // the string, untouched
 * hydrateAppointmentStatus( [ '@type' => 'AppointmentNoShow' ] ) ;              // AppointmentNoShow
 * hydrateAppointmentStatus( [ 'additionalType' => AppointmentStatus::DONE ] ) ; // AppointmentDone
 * ```
 */
function hydrateAppointmentStatus( mixed $init = null ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $statuses = array_map
        (
            fn( $status ) => hydrateAppointmentStatus( $status ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $statuses , fn( $status ) => $status instanceof AppointmentStatus || is_scalar( $status ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    static $members = null ;

    $members ??=
    [
        AppointmentStatus::CANCELLED => AppointmentCancelled::class ,
        AppointmentStatus::DONE      => AppointmentDone::class      ,
        AppointmentStatus::NO_SHOW   => AppointmentNoShow::class    ,
        AppointmentStatus::PLANNED   => AppointmentPlanned::class   ,
    ];

    $class = findEnumerationMember( $init , $members , AppointmentStatus::class ) ;

    return new $class( $init ) ;
}
