<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;
use org\schema\DefinedTerm;
use org\schema\Offer;
use org\schema\Organization;
use org\schema\QuantitativeValue;
use org\schema\VirtualLocation;
use org\schema\enumerations\events\EventCancelled;
use org\schema\enumerations\events\EventStatusType;

use xyz\oihana\schema\appointments\CustomerAppointment;
use xyz\oihana\schema\appointments\FollowUp;
use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\enumerations\AppointmentDone;
use xyz\oihana\schema\enumerations\AppointmentStatus;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\organizations\Subsidiary;
use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\people\Seller;
use xyz\oihana\schema\places\CustomerSite;
use xyz\oihana\schema\products\Product;

use function xyz\oihana\schema\helpers\hydrate\appointments\hydrateCustomerAppointment;

final class HydrateCustomerAppointmentTest extends TestCase
{
    /**
     * @return array<string,mixed>
     */
    private static function payload(): array
    {
        return
        [
            'name'              => 'Meeting with Acme Corporation' ,
            'startDate'         => '2026-09-01T10:00:00+02:00' ,
            'customer'          => [ 'name' => 'Acme Corporation' , 'address' => [ 'streetAddress' => '1 Example street' ] ] ,
            'attendee'          => [ [ 'name' => 'Jane Doe' , 'jobTitle' => [ 'id' => 'BUYER' ] , 'workLocation' => [ 'name' => 'Head office' ] ] ] ,
            'organizer'         => [ 'name' => 'Alice Smith' ] ,
            'assignedCompany'   => [ Schema::AT_TYPE => 'Subsidiary' , 'name' => 'Acme Corporation' ] ,
            'assignedSeller'    => [ 'name' => 'Richard Roe' ] ,
            'location'          => [ Schema::AT_TYPE => 'CustomerSite' , 'name' => 'Head office' ] ,
            'appointmentType'   => [ 'id' => 'ONSITE' , 'name' => 'On site' ] ,
            'tags'              => [ [ 'id' => 'DEMO' ] , [ 'id' => 'MEAL' ] ] ,
            'makesOffer'        => [ [ 'itemOffered' => [ 'name' => 'Model A widget' ] , 'eligibleQuantity' => [ 'value' => 10 ] ] ] ,
            'eventStatus'       => [ Schema::AT_TYPE => 'EventCancelled' , Schema::DESCRIPTION => 'Called off the day before.' ] ,
            'appointmentStatus' => [ Schema::ADDITIONAL_TYPE => AppointmentStatus::DONE ] ,
            'report'            =>
            [
                'mood'     => [ 'id' => 'SATISFIED' ] ,
                'attendee' => [ [ 'name' => 'Jane Doe' ] ] ,
                'author'   => [ Schema::AT_TYPE => 'Person' , 'name' => 'Richard Roe' ] ,
                'followUp' => [ [ 'followUpType' => [ 'id' => 'CALL_BACK' ] , 'result' => [ 'name' => 'Second meeting' , 'report' => [ 'mood' => [ 'id' => 'NEUTRAL' ] ] ] ] ] ,
            ] ,
        ];
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAWholeMeeting(): void
    {
        $appointment = hydrateCustomerAppointment( self::payload() ) ;

        $this->assertInstanceOf( CustomerAppointment::class , $appointment ) ;
        $this->assertInstanceOf( Customer::class    , $appointment->customer ) ;
        $this->assertInstanceOf( User::class        , $appointment->organizer ) ;
        $this->assertInstanceOf( Subsidiary::class  , $appointment->assignedCompany ) ;
        $this->assertInstanceOf( Seller::class      , $appointment->assignedSeller ) ;
        $this->assertInstanceOf( CustomerSite::class, $appointment->location ) ;
        $this->assertInstanceOf( DefinedTerm::class , $appointment->appointmentType ) ;
        $this->assertInstanceOf( VisitReport::class , $appointment->report ) ;

        $this->assertContainsOnlyInstancesOf( CustomerEmployee::class , $appointment->attendee ) ;
        $this->assertContainsOnlyInstancesOf( DefinedTerm::class      , $appointment->tags ) ;
        $this->assertContainsOnlyInstancesOf( Offer::class            , $appointment->makesOffer ) ;

        // The nested references of the nested references are typed too : one call is enough.
        $this->assertInstanceOf( DefinedTerm::class  , $appointment->attendee[ 0 ]->jobTitle ) ;
        $this->assertInstanceOf( CustomerSite::class , $appointment->attendee[ 0 ]->workLocation ) ;
        $this->assertInstanceOf( DefinedTerm::class  , $appointment->report->mood ) ;
        $this->assertContainsOnlyInstancesOf( FollowUp::class , $appointment->report->followUp ) ;
    }

    /**
     * What the salesperson means to show : the offer is typed, and so is the item it
     * wraps — with this package's own product, so an offered item keeps its commerce
     * properties rather than coming back as the bare Schema.org class.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testTheOffersCarryTheBusinessProduct(): void
    {
        $appointment = hydrateCustomerAppointment( self::payload() ) ;
        $offer       = $appointment->makesOffer[ 0 ] ;

        $this->assertInstanceOf( Product::class           , $offer->itemOffered ) ;
        $this->assertInstanceOf( QuantitativeValue::class , $offer->eligibleQuantity ) ;
        $this->assertSame( 'Model A widget' , $offer->itemOffered->name ) ;
    }

    /**
     * A status written as the member class comes back as the member class, with the
     * reason it carried — and the identifier it answers is the one the bare constant
     * answers, on both axes.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testBothStatusAxesComeBackTyped(): void
    {
        $appointment = hydrateCustomerAppointment( self::payload() ) ;

        $this->assertInstanceOf( EventCancelled::class , $appointment->eventStatus ) ;
        $this->assertSame( EventStatusType::CANCELLED , $appointment->eventStatus->additionalType ) ;
        $this->assertSame( 'Called off the day before.' , $appointment->eventStatus->description ) ;

        $this->assertInstanceOf( AppointmentDone::class , $appointment->appointmentStatus ) ;
        $this->assertSame( AppointmentStatus::DONE , $appointment->appointmentStatus->additionalType ) ;
    }

    /**
     * 🚨 The meeting a follow-up names is a reference : it is typed one level and no
     * further, so reading a meeting cannot walk the chain meeting → report →
     * follow-up → meeting for as long as the data holds.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testTheMeetingAFollowUpNamesIsNotUnfolded(): void
    {
        $appointment = hydrateCustomerAppointment( self::payload() ) ;
        $result      = $appointment->report->followUp[ 0 ]->result ;

        $this->assertInstanceOf( CustomerAppointment::class , $result ) ;
        $this->assertSame( 'Second meeting' , $result->name ) ;
        $this->assertIsArray( $result->report ) ;
    }

    /**
     * 🔑 Three properties are left to their attribute, because reflection already
     * settles them from the payload's `@type` : forcing a class over them would read a
     * plain organization back as a subsidiary, or a virtual room as a customer site.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testThePropertiesTheirAttributeAlreadySettlesAreLeftAlone(): void
    {
        $appointment = hydrateCustomerAppointment
        ([
            'assignedCompany' => [ Schema::AT_TYPE => 'Organization' , 'name' => 'Acme Corporation' ] ,
            'location'        => [ Schema::AT_TYPE => 'VirtualLocation' , 'url' => 'https://example.org/room/1' ] ,
        ]) ;

        $this->assertInstanceOf( Organization::class , $appointment->assignedCompany ) ;
        $this->assertNotInstanceOf( Subsidiary::class , $appointment->assignedCompany ) ;

        $this->assertInstanceOf( VirtualLocation::class , $appointment->location ) ;
    }

    /**
     * ⚠️ `assignedSeller` declares no attribute and its union names a plain `Person`,
     * so reflection can only ever answer a person : it is the one property the helper
     * has to re-read.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testTheAssignedSellerIsReadBackAsASeller(): void
    {
        $appointment = hydrateCustomerAppointment( [ 'assignedSeller' => [ 'name' => 'Richard Roe' ] ] ) ;

        $this->assertInstanceOf( Seller::class , $appointment->assignedSeller ) ;
        $this->assertSame( 'Richard Roe' , $appointment->assignedSeller->name ) ;

        // A list of them is read as a list.
        $several = hydrateCustomerAppointment( [ 'assignedSeller' => [ [ 'name' => 'Richard Roe' ] , [ 'name' => 'Alice Smith' ] ] ] ) ;
        $this->assertCount( 2 , $several->assignedSeller ) ;
        $this->assertContainsOnlyInstancesOf( Seller::class , $several->assignedSeller ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $appointments = hydrateCustomerAppointment
        ([
            [ 'name' => 'Meeting with Acme Corporation' ] ,
            [ 'name' => 'Meeting with Acme Corporation, again' ] ,
        ]) ;

        $this->assertIsArray( $appointments ) ;
        $this->assertCount( 2 , $appointments ) ;
        $this->assertContainsOnlyInstancesOf( CustomerAppointment::class , $appointments ) ;

        $this->assertNull( hydrateCustomerAppointment( [] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateCustomerAppointment() ) ;
        $this->assertSame( 'appointment-ref-42' , hydrateCustomerAppointment( 'appointment-ref-42' ) ) ;

        $appointment = new CustomerAppointment() ;
        $this->assertSame( $appointment , hydrateCustomerAppointment( $appointment ) ) ;
    }

    /**
     * A reference nobody joined yet stays what it was, and a status stated as the bare
     * constant crosses untouched — the two forms being what the pair of helpers exists
     * for.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testLeavesUnresolvedReferencesAndBareConstantsUntouched(): void
    {
        $appointment = hydrateCustomerAppointment
        ([
            'assignedSeller'    => 'seller-ref-42' ,
            'location'          => 'site-ref-42' ,
            'appointmentType'   => 'ONSITE' ,
            'eventStatus'       => EventStatusType::SCHEDULED ,
            'appointmentStatus' => AppointmentStatus::PLANNED ,
        ]) ;

        $this->assertSame( 'seller-ref-42' , $appointment->assignedSeller ) ;
        $this->assertSame( 'site-ref-42'   , $appointment->location ) ;
        $this->assertSame( 'ONSITE'        , $appointment->appointmentType ) ;
        $this->assertSame( EventStatusType::SCHEDULED  , $appointment->eventStatus ) ;
        $this->assertSame( AppointmentStatus::PLANNED  , $appointment->appointmentStatus ) ;
    }

    /**
     * An empty list is not a list of things : every nested reference answers the same
     * through the meeting as through the nested helper called on its own.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testEmptyNestedListsYieldNullNotAnEmptyArray(): void
    {
        $appointment = hydrateCustomerAppointment
        ([
            'attendee'   => [] ,
            'tags'       => [] ,
            'makesOffer' => [] ,
            'customer'   => [] ,
        ]) ;

        $this->assertNull( $appointment->attendee ) ;
        $this->assertNull( $appointment->tags ) ;
        $this->assertNull( $appointment->makesOffer ) ;
        $this->assertNull( $appointment->customer ) ;
    }

    /**
     * 🔑 **A bare reference survives inside a list**, in a nested branch as much as in the
     * helper's own : a salesperson nobody joined yet is a handle. The keys stay gap-free —
     * a filtered list left with holes serializes as a JSON object.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testAListOfSellerReferencesSurvivesAndKeepsItsKeys(): void
    {
        $appointment = hydrateCustomerAppointment( [ 'assignedSeller' => [ 'RROE' , [ 'name' => 'Richard Roe' ] ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $appointment->assignedSeller ) ) ;
        $this->assertSame( 'RROE' , $appointment->assignedSeller[ 0 ] ) ;
        $this->assertInstanceOf( Seller::class , $appointment->assignedSeller[ 1 ] ) ;

        // And a list of meetings, one level up.
        $this->assertSame( [ 'appointment-ref-42' ] , hydrateCustomerAppointment( [ 'appointment-ref-42' ] ) ) ;
    }
}
