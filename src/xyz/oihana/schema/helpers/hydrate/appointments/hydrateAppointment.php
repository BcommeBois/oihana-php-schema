<?php

namespace xyz\oihana\schema\helpers\hydrate\appointments;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;
use org\schema\DefinedTerm;
use org\schema\Organization;
use org\schema\Person;

use xyz\oihana\schema\appointments\Appointment;
use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\products\Product;
use xyz\oihana\schema\thesaurus\ThesaurusTerm;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateDefinedTerm;
use function org\schema\helpers\hydrate\hydrateEventStatus;
use function org\schema\helpers\hydrate\hydrateOffer;
use function org\schema\helpers\hydrate\hydrateOrganizationOrPerson;

use function xyz\oihana\schema\helpers\hydrate\hydrateCustomer;
use function xyz\oihana\schema\helpers\hydrate\hydrateCustomerEmployee;
use function xyz\oihana\schema\helpers\hydrate\termClassOf;

/**
 * Hydrate an array definition with the Appointment class, or with a subclass of it.
 *
 * Handles both a single appointment array and an array of appointments.
 *
 * What it resolves is everything a meeting may carry, and every polymorphic case is
 * settled by **the stored type of the value itself** — never by a class this helper
 * would impose :
 *
 * - `eventStatus` and `appointmentStatus` — the two axes of state, each read back as the
 *   member class holding its URI ;
 * - `appointmentType` and `tags` — the vocabularies of the meeting itself ;
 * - `about` — the counterpart : a value announcing a customer is read back as one, and
 *   anything else falls back on its `@type`, an organization or a person ;
 * - `attendee` — entry by entry : an account, a customer contact, or a plain person or
 *   organization — one table may seat them together ;
 * - `makesOffer` — what one means to put in front of the counterpart ;
 * - `report` — by its stored type : a visit's write-up comes back with its richer class,
 *   any other with the common one, each resolved in depth by its own helper.
 *
 * The diary (`organizer`) needs no resolver : its attribute says enough for reflection
 * to answer it.
 *
 * 🔑 **The target class is a parameter**, so a caller with a subclass of its own reuses
 * this whole body rather than copying it.
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

    // The report keeps the meeting's term map until the caller names a branch for it :
    // `tags` is declared on the meeting and on the report over two different families of
    // terms, and the branch is what tells them apart.
    $reportClass = is_array( $termClass ) ? ( $termClass[ Appointment::REPORT ] ?? $termClass ) : $termClass ;

    // One attendee, resolved on the type its stored copy carries : an account, a customer
    // contact — or, with no stored type to read, a person unless its `@type` claims an
    // organization. A scalar entry is an unresolved reference and is kept as it stands.
    $attendee = static function( mixed $entry ) use ( $reflection ) :mixed
    {
        if( !is_array( $entry ) )
        {
            return $entry ;
        }

        $stored = $entry[ Schema::ADDITIONAL_TYPE ] ?? null ;

        if( $stored === User::getSchemaType() )
        {
            return $reflection->hydrate( $entry , User::class ) ;
        }

        if( $stored === CustomerEmployee::getSchemaType() )
        {
            return hydrateCustomerEmployee( $entry ) ;
        }

        $type = $entry[ Schema::AT_TYPE ] ?? null ;

        return is_string( $type ) && strtolower( $type ) === 'organization'
             ? $reflection->hydrate( $entry , Organization::class )
             : $reflection->hydrate( $entry , Person::class ) ;
    } ;

    // Read from the raw payload : what reflection made of these properties is either
    // unresolvable from the property type alone, or shallower than the helper's answer.
    // Every polymorphic case resolves on the STORED type of the value, never on a class
    // imposed from here.

    $resolvers =
    [
        Schema::EVENT_STATUS             => hydrateEventStatus( ... )       ,
        Appointment::APPOINTMENT_STATUS  => hydrateAppointmentStatus( ... ) ,
        Appointment::APPOINTMENT_TYPE    => fn( $raw ) => hydrateDefinedTerm( $raw , termClassOf( $termClass , Appointment::APPOINTMENT_TYPE ) ) ,
        Appointment::TAGS                => fn( $raw ) => hydrateDefinedTerm( $raw , termClassOf( $termClass , Appointment::TAGS ) ) ,

        Schema::ABOUT => fn( array $raw ) => ( $raw[ Schema::ADDITIONAL_TYPE ] ?? null ) === Customer::getSchemaType()
                       ? hydrateCustomer( $raw )
                       : hydrateOrganizationOrPerson( $raw ) ,

        Schema::ATTENDEE => static function( array $raw ) use ( $attendee ) :mixed
        {
            if( !isIndexed( $raw ) )
            {
                return $attendee( $raw ) ;
            }

            $attendees = array_map( $attendee , $raw ) ;

            // A scalar entry is an unresolved reference and is kept as it stands ; only an
            // entry that WAS an array and gave nothing is dropped.
            $filtered = array_values( array_filter( $attendees , static fn( $entry ) => $entry instanceof Person || $entry instanceof Organization || is_scalar( $entry ) ) ) ;

            return count( $filtered ) > 0 ? $filtered : null ;
        } ,

        Appointment::MAKES_OFFER => fn( array $raw ) => hydrateOffer( $raw , Product::class ) ,

        Appointment::REPORT => fn( array $raw ) => ( $raw[ Schema::ADDITIONAL_TYPE ] ?? null ) === VisitReport::getSchemaType()
                             ? hydrateVisitReport( $raw , $reportClass )
                             : hydrateMeetingReport( $raw , $reportClass ) ,
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
