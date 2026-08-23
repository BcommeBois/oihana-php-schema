<?php

namespace xyz\oihana\schema\helpers\hydrate\appointments;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;

use xyz\oihana\schema\appointments\CustomerAppointment;
use xyz\oihana\schema\people\Seller;
use xyz\oihana\schema\products\Product;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateDefinedTerm;
use function org\schema\helpers\hydrate\hydrateEventStatus;
use function org\schema\helpers\hydrate\hydrateOffer;

use function xyz\oihana\schema\helpers\hydrate\hydrateCustomer;
use function xyz\oihana\schema\helpers\hydrate\hydrateCustomerEmployee;

/**
 * Hydrate an array definition with the CustomerAppointment class.
 *
 * The head of the appointments family, and the mirror of
 * {@see \xyz\oihana\schema\helpers\hydrate\documents\hydrateBusinessDocument()} : one call
 * on a stored meeting gives back a meeting whose customer, contacts, place, offers, report
 * and both statuses are typed.
 *
 * Handles both a single meeting array and an array of meetings. The meeting itself is
 * built through {@see Reflection::hydrate()}, which honors every `#[HydrateAs]` /
 * `#[HydrateWith]` attribute declared across the hierarchy — a constructor would not, and
 * everything the helper does not name would stay a raw array.
 *
 * 🔑 **Three properties are left to their attribute, on purpose.** `organizer`
 * (`#[HydrateAs(User::class)]`), `assignedCompany` (`#[HydrateWith(Subsidiary, Organization)]`)
 * and `location` (`#[HydrateWith(CustomerSite, JobSite, Place, PostalAddress, VirtualLocation)]`)
 * are already resolved exactly by reflection, from the payload's `@type`. Forcing a class
 * over them would do worse than nothing : it would read back a plain `Organization` as a
 * subsidiary, or a virtual room as a customer site.
 *
 * ⚠️ **`assignedSeller` is the one that needs a hand.** It declares no attribute and its
 * union names a single `Person`, so reflection can only ever answer a plain person : it is
 * re-read from the payload as a {@see Seller}, the class the property means.
 *
 * What is re-resolved from the raw payload, and why :
 *
 * - `customer` → {@see hydrateCustomer()}, the class the property names first ;
 * - `attendee` → {@see hydrateCustomerEmployee()}, which also types each contact's own
 *   references ;
 * - `appointmentType` (one term) and `tags` (several) → {@see hydrateDefinedTerm()} ;
 * - `makesOffer` → {@see hydrateOffer()}, given this package's own {@see Product} so an
 *   offered item keeps its commerce properties ;
 * - `report` → {@see hydrateVisitReport()}, and with it the follow-ups it carries ;
 * - `eventStatus` → {@see hydrateEventStatus()}, `appointmentStatus` →
 *   {@see hydrateAppointmentStatus()} : a status written as the member class comes back as
 *   the member class, with the reason it carried.
 *
 * Each of those happens only when the raw payload holds an array under the property — when
 * there is something to hydrate. The resolved value is then written as is, `null` included :
 * an array that resolves to nothing becomes `null`, never a leftover raw array. Anything
 * else is left to whatever {@see Reflection::hydrate()} made of it.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed $init Single meeting data or array of meeting data.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 *
 * @example
 * ```php
 * $appointment = hydrateCustomerAppointment
 * ([
 *     'name'              => 'Meeting with Acme Corporation' ,
 *     'startDate'         => '2026-09-01T10:00:00+02:00' ,
 *     'customer'          => [ 'name' => 'Acme Corporation' ] ,
 *     'attendee'          => [ [ 'name' => 'Jane Doe' ] ] ,
 *     'appointmentStatus' => [ '@type' => 'AppointmentDone' ] ,
 *     'report'            => [ 'followUp' => [ [ 'followUpType' => [ 'id' => 'CALL_BACK' ] ] ] ] ,
 * ]) ;
 *
 * $appointment->customer instanceof Customer            ; // true
 * $appointment->report->followUp[ 0 ] instanceof FollowUp ; // true
 * ```
 */
function hydrateCustomerAppointment( mixed $init = null ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $appointments = array_map
        (
            fn( $appointment ) => hydrateCustomerAppointment( $appointment ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $appointments , fn( $appointment ) => $appointment instanceof CustomerAppointment || is_scalar( $appointment ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    // The hydration plan is cached by the Reflection instance : keep it across calls so a
    // list of meetings costs one plan, not one plan per meeting.
    static $reflection = null ;

    $reflection ??= new Reflection() ;

    $appointment = $reflection->hydrate( $init , CustomerAppointment::class ) ;

    // Read from the raw payload : what reflection made of these properties is either
    // unresolvable from the property type alone, or shallower than the helper's answer.

    $resolvers =
    [
        Schema::CUSTOMER                        => hydrateCustomer( ... )          ,
        Schema::ATTENDEE                        => hydrateCustomerEmployee( ... )  ,
        Schema::MAKES_OFFER                     => fn( $raw ) => hydrateOffer( $raw , Product::class ) ,
        Schema::EVENT_STATUS                    => hydrateEventStatus( ... )       ,
        CustomerAppointment::APPOINTMENT_STATUS => hydrateAppointmentStatus( ... ) ,
        CustomerAppointment::APPOINTMENT_TYPE   => hydrateDefinedTerm( ... )       ,
        CustomerAppointment::TAGS               => hydrateDefinedTerm( ... )       ,
        CustomerAppointment::REPORT             => hydrateVisitReport( ... )       ,
    ];

    foreach( $resolvers as $property => $resolve )
    {
        $raw = $init[ $property ] ?? null ;
        if( is_array( $raw ) )
        {
            $appointment->{ $property } = $resolve( $raw ) ;
        }
    }

    // ------- assignedSeller
    // No attribute, and a union naming a plain Person : reflection cannot answer the Seller
    // the property means.

    $assignedSeller = $init[ CustomerAppointment::ASSIGNED_SELLER ] ?? null ;
    if( is_array( $assignedSeller ) )
    {
        if( isIndexed( $assignedSeller ) )
        {
            $sellers = array_map
            (
                fn( $seller ) => is_array( $seller ) ? $reflection->hydrate( $seller , Seller::class ) : $seller ,
                $assignedSeller
            );

            // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
            // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
            $filtered = array_values( array_filter( $sellers , fn( $seller ) => $seller instanceof Seller || is_scalar( $seller ) ) ) ;

            $appointment->assignedSeller = count( $filtered ) > 0 ? $filtered : null ;
        }
        else
        {
            $appointment->assignedSeller = $reflection->hydrate( $assignedSeller , Seller::class ) ;
        }
    }

    return $appointment ;
}
