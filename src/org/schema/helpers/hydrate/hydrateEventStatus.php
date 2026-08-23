<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use org\schema\enumerations\events\EventCancelled;
use org\schema\enumerations\events\EventMovedOnline;
use org\schema\enumerations\events\EventPostponed;
use org\schema\enumerations\events\EventRescheduled;
use org\schema\enumerations\events\EventScheduled;
use org\schema\enumerations\events\EventStatusType;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array definition with the member class of {@see EventStatusType} it names.
 *
 * A status is written one of two ways : the bare constant, when there is nothing more
 * to say, or the member class, when there is — `new EventCancelled([ 'description' => '…' ])`
 * says why an event was called off, and a string cannot. Stored and read back, the second
 * form comes back as a plain array like any other object, and this helper turns it into
 * the member class again.
 *
 * 🔑 **The bare constant comes back untouched.** It is not an array, so it falls through
 * the first guard — which is the whole point : a consumer must be able to hand this helper
 * whichever form was stored without knowing which one it was.
 *
 * The member is resolved by {@see findEnumerationMember()}, from the payload's
 * `additionalType` — the URI every member states of itself since 1.5.0 — then from its
 * `@type`, which carries the short name. Nothing recognized gives an {@see EventStatusType} :
 * the status is unknown to this vocabulary, and answering `null` would throw away the
 * reason it carried.
 *
 * ⚠️ A status is single-valued. An indexed array is still read as a list, for consistency
 * with the rest of the family, and answers `null` when nothing in it resolves.
 *
 * @param mixed $init Single status data, a list of such data, or any other value.
 *
 * @return mixed
 *
 * @throws ReflectionException
 *
 * @example
 * ```php
 * hydrateEventStatus( EventStatusType::CANCELLED ) ;                        // the string, untouched
 * hydrateEventStatus( [ '@type' => 'EventCancelled' ] ) ;                    // EventCancelled
 * hydrateEventStatus( [ 'additionalType' => EventStatusType::POSTPONED ] ) ; // EventPostponed
 * ```
 */
function hydrateEventStatus( mixed $init = null ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $statuses = array_map
        (
            fn( $status ) => hydrateEventStatus( $status ) ,
            $init
        );

        $filtered = array_values( array_filter( $statuses , fn( $status ) => $status instanceof EventStatusType ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    static $members = null ;

    $members ??=
    [
        EventStatusType::CANCELLED    => EventCancelled::class   ,
        EventStatusType::MOVED_ONLINE => EventMovedOnline::class ,
        EventStatusType::POSTPONED    => EventPostponed::class   ,
        EventStatusType::RESCHEDULED  => EventRescheduled::class ,
        EventStatusType::SCHEDULED    => EventScheduled::class   ,
    ];

    $class = findEnumerationMember( $init , $members , EventStatusType::class ) ;

    return new $class( $init ) ;
}
