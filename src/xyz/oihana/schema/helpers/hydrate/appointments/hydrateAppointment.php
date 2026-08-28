<?php

namespace xyz\oihana\schema\helpers\hydrate\appointments;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;
use org\schema\DefinedTerm;

use xyz\oihana\schema\appointments\Appointment;
use xyz\oihana\schema\thesaurus\ThesaurusTerm;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateDefinedTerm;
use function org\schema\helpers\hydrate\hydrateEventStatus;

use function xyz\oihana\schema\helpers\hydrate\termClassOf;

/**
 * Hydrate an array definition with the Appointment class, or with a subclass of it.
 *
 * Handles both a single appointment array and an array of appointments.
 *
 * What it resolves is what every meeting carries, whoever it is with :
 *
 * - `eventStatus` and `appointmentStatus` — the two axes of state, each read back as the
 *   member class holding its URI ;
 * - `appointmentType` and `tags` — the vocabularies of the meeting itself.
 *
 * The diary (`organizer`) and the company (`assignedCompany`) need no resolver : their
 * attributes say enough for reflection to answer them.
 *
 * ⚠️ **`about` and `attendee` are deliberately left alone here.** Whom a meeting is with and
 * who may be invited to it are exactly what tells one family from another — this helper
 * cannot know, and guessing would read every meeting back as the wrong kind. Each family
 * resolves them after this one has run, which is what {@see hydrateCustomerAppointment()}
 * and {@see hydrateInternalMeeting()} do.
 *
 * ⚠️ **The report comes back typed but shallow.** Its attribute is enough for reflection to
 * build a {@see \xyz\oihana\schema\appointments\MeetingReport}, and no more : what is
 * inside it — the promises, the vocabularies — is resolved by the report helper the family
 * names, which is a class this one has no reason to choose.
 *
 * 🔑 **The target class is a parameter**, so a family reuses this whole body rather than
 * copying it.
 *
 * @param mixed $init Single appointment data or array of appointment data.
 * @param class-string<DefinedTerm>|array<string,class-string<DefinedTerm>|array<string,class-string<DefinedTerm>>> $termClass
 *        The class the term properties are hydrated into, or a map naming them one by one.
 *        See {@see termClassOf()}.
 * @param class-string<Appointment> $class The class to build — a subclass of {@see Appointment}, or itself.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 *
 * @example
 * ```php
 * $meeting = hydrateAppointment
 * ([
 *     'startDate'         => '2035-09-01T10:00:00+02:00' ,
 *     'appointmentStatus' => 'https://schema.oihana.xyz/AppointmentPlanned' ,
 *     'appointmentType'   => [ 'id' => 'CALL' ] ,
 * ]);
 *
 * $meeting->appointmentType instanceof ThesaurusTerm ; // true
 * ```
 */
function hydrateAppointment( mixed $init = null , string|array $termClass = ThesaurusTerm::class , string $class = Appointment::class ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $appointments = array_map
        (
            fn( $appointment ) => hydrateAppointment( $appointment , $termClass , $class ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $appointments , fn( $appointment ) => $appointment instanceof $class || is_scalar( $appointment ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    // The hydration plan is cached by the Reflection instance : keep it across calls so a
    // list of meetings costs one plan, not one plan per meeting.
    static $reflection = null ;

    $reflection ??= new Reflection() ;

    $appointment = $reflection->hydrate( $init , $class ) ;

    // Read from the raw payload : what reflection made of these properties is either
    // unresolvable from the property type alone, or shallower than the helper's answer.

    $resolvers =
    [
        Schema::EVENT_STATUS             => hydrateEventStatus( ... )       ,
        Appointment::APPOINTMENT_STATUS  => hydrateAppointmentStatus( ... ) ,
        Appointment::APPOINTMENT_TYPE    => fn( $raw ) => hydrateDefinedTerm( $raw , termClassOf( $termClass , Appointment::APPOINTMENT_TYPE ) ) ,
        Appointment::TAGS                => fn( $raw ) => hydrateDefinedTerm( $raw , termClassOf( $termClass , Appointment::TAGS ) ) ,
    ];

    foreach( $resolvers as $property => $resolve )
    {
        $raw = $init[ $property ] ?? null ;
        if( is_array( $raw ) )
        {
            $appointment->{ $property } = $resolve( $raw ) ;
        }
    }

    return $appointment ;
}
