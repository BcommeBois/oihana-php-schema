<?php

namespace xyz\oihana\schema\helpers\hydrate\appointments;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;
use org\schema\DefinedTerm;

use xyz\oihana\schema\appointments\InternalMeeting;
use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\thesaurus\ThesaurusTerm;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array definition with the InternalMeeting class.
 *
 * Handles both a single meeting array and an array of meetings.
 *
 * 🔑 **What every meeting carries is resolved by {@see hydrateAppointment()}** — the two
 * axes of state, the kind, the qualifiers — and this helper asks it for an
 * {@see InternalMeeting} rather than copying its body. What is left here is what only an
 * internal meeting knows :
 *
 * - `attendee` — the colleagues expected, read back as {@see User}. Nobody outside is
 *   invited to one, so the union the parent leaves wide narrows to the accounts that hold a
 *   diary ;
 * - `report` — a {@see \xyz\oihana\schema\appointments\MeetingReport}, the common one : a
 *   meeting between colleagues brings back no mood and no outcome to publish.
 *
 * ⚠️ **`about` stays empty**, and that is the definition of the family : an internal meeting
 * has no counterpart. A payload carrying one is left exactly as it stands rather than read
 * back as something this class does not mean.
 *
 * @param mixed $init Single meeting data or array of meeting data.
 * @param class-string<DefinedTerm>|array<string,class-string<DefinedTerm>|array<string,class-string<DefinedTerm>>> $termClass
 *        The class the term properties are hydrated into, or a map naming them one by one.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 *
 * @example
 * ```php
 * $meeting = hydrateInternalMeeting
 * ([
 *     'name'      => 'Weekly review' ,
 *     'startDate' => '2035-09-01T09:00:00+02:00' ,
 *     'attendee'  => [ [ 'givenName' => 'Alice' , 'familyName' => 'Smith' ] ] ,
 * ]);
 *
 * $meeting->attendee[ 0 ] instanceof User ; // true
 * ```
 */
function hydrateInternalMeeting( mixed $init = null , string|array $termClass = ThesaurusTerm::class ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $meetings = array_map
        (
            fn( $meeting ) => hydrateInternalMeeting( $meeting , $termClass ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $meetings , fn( $meeting ) => $meeting instanceof InternalMeeting || is_scalar( $meeting ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $meeting = hydrateAppointment( $init , $termClass , InternalMeeting::class ) ;

    if( $meeting instanceof InternalMeeting )
    {
        // The hydration plan is cached by the Reflection instance : keep it across calls so a
        // list of meetings costs one plan, not one plan per meeting.
        static $reflection = null ;

        $reflection ??= new Reflection() ;

        // ------- attendee

        $attendee = $init[ Schema::ATTENDEE ] ?? null ;
        if( is_array( $attendee ) )
        {
            $rows = isIndexed( $attendee ) ? $attendee : [ $attendee ] ;

            $accounts = array_map
            (
                fn( $account ) => is_array( $account ) ? $reflection->hydrate( $account , User::class ) : $account ,
                $rows
            );

            $filtered = array_values( array_filter( $accounts , fn( $account ) => $account instanceof User || is_scalar( $account ) ) ) ;

            $meeting->attendee = count( $filtered ) > 0 ? $filtered : null ;
        }

        // ------- report

        $report = $init[ InternalMeeting::REPORT ] ?? null ;
        if( is_array( $report ) )
        {
            $reportClass = is_array( $termClass ) ? ( $termClass[ InternalMeeting::REPORT ] ?? $termClass ) : $termClass ;

            $meeting->report = hydrateMeetingReport( $report , $reportClass ) ;
        }

    }

    return $meeting ;
}
