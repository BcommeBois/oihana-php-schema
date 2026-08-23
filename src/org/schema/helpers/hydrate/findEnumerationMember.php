<?php

namespace org\schema\helpers\hydrate;

use org\schema\constants\Schema;

/**
 * Find the enumeration member class a payload names.
 *
 * An enumeration of this package states itself two ways : the bare constant, when there
 * is nothing more to say, and the member class, when there is — a status carrying the
 * reason it was set. Stored and read back, the second form is a plain array, and turning
 * it into the member class again is the same walk for every enumeration : hence one
 * function rather than one per family.
 *
 * The member is read from the payload in this order :
 *
 * - `additionalType`, the URI every member states of itself since 1.5.0 — the same string
 *   the bare constant carries, so both forms filter alike ;
 * - `@type`, which carries the **short name** of the class (`EventCancelled`), never the
 *   URI — that is what the serialization writes, and what a payload written before
 *   `additionalType` existed carries alone.
 *
 * ⚠️ **The two keys do not say the same thing**, which is why both are read : a
 * vocabulary is free to spell its URIs otherwise than its class names — `AppointmentStatus`
 * spells `…#NoShow` where the class is `AppointmentNoShow` — so neither key can be derived
 * from the other.
 *
 * When nothing is recognized, `$default` is answered : the enumeration head itself, so an
 * unknown status keeps the reason it carried rather than being dropped.
 *
 * @param array                $init    The raw payload of the member.
 * @param array<string,string> $members The member classes of the enumeration, keyed by the URI each one states.
 * @param string               $default The class answered when the payload names nothing known.
 *
 * @return string The class to hydrate the payload into.
 *
 * @example
 * ```php
 * findEnumerationMember( [ '@type' => 'EventCancelled' ] , $members , EventStatusType::class ) ;
 * ```
 */
function findEnumerationMember( array $init , array $members , string $default ) :string
{
    $additionalType = $init[ Schema::ADDITIONAL_TYPE ] ?? null ;
    if( is_string( $additionalType ) && isset( $members[ $additionalType ] ) )
    {
        return $members[ $additionalType ] ;
    }

    $type = $init[ Schema::AT_TYPE ] ?? null ;
    if( is_string( $type ) )
    {
        foreach( $members as $member )
        {
            if( str_ends_with( $member , '\\' . $type ) )
            {
                return $member ;
            }
        }
    }

    return $default ;
}
