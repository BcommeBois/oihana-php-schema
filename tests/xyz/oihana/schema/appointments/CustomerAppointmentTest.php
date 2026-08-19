<?php

namespace tests\xyz\oihana\schema\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;
use org\schema\Event;
use org\schema\Offer;
use org\schema\Product;

use xyz\oihana\schema\appointments\CustomerAppointment;
use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\AppointmentStatus;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\people\Seller;
use xyz\oihana\schema\places\CustomerSite;

class CustomerAppointmentTest extends TestCase
{
    public function testIsAnEvent(): void
    {
        $appointment = new CustomerAppointment() ;
        $this->assertInstanceOf( Event::class , $appointment );
    }

    public function testSchemaType(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/CustomerAppointment' , CustomerAppointment::getSchemaType() );
    }

    public function testPropertiesDefaultToNull(): void
    {
        $appointment = new CustomerAppointment() ;

        $this->assertNull( $appointment->appointmentStatus ?? null );
        $this->assertNull( $appointment->appointmentType   ?? null );
        $this->assertNull( $appointment->assignedSeller    ?? null );
        $this->assertNull( $appointment->customer          ?? null );
        $this->assertNull( $appointment->makesOffer        ?? null );
        $this->assertNull( $appointment->report            ?? null );
        $this->assertNull( $appointment->tags              ?? null );
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testConstructorCopiesTheProperties(): void
    {
        $appointment = new CustomerAppointment
        ([
            Schema::NAME                => 'Product range review' ,
            Schema::START_DATE          => '2026-09-03T09:30:00+02:00' ,
            Schema::END_DATE            => '2026-09-03T10:30:00+02:00' ,
            Oihana::APPOINTMENT_TYPE    => 'VISIT' ,
            Oihana::APPOINTMENT_STATUS  => AppointmentStatus::PLANNED ,
            Oihana::TAGS                => [ 'MEAL' , 'DEMO' ] ,
        ]);

        $this->assertSame( 'Product range review'                          , $appointment->name              );
        $this->assertSame( '2026-09-03T09:30:00+02:00'                     , $appointment->startDate         );
        $this->assertSame( '2026-09-03T10:30:00+02:00'                     , $appointment->endDate           );
        $this->assertSame( 'VISIT'                                          , $appointment->appointmentType   );
        $this->assertSame( AppointmentStatus::PLANNED                       , $appointment->appointmentStatus );
        $this->assertSame( [ 'MEAL' , 'DEMO' ]                           , $appointment->tags              );
    }

    /**
     * The two axes are written side by side and neither replaces the other : the
     * slot moved, the meeting has still to happen.
     * @return void
     * @throws ReflectionException
     */
    public function testTheSlotStatusAndTheMeetingStatusAreIndependent(): void
    {
        $appointment = new CustomerAppointment
        ([
            Schema::EVENT_STATUS       => 'https://schema.org/EventRescheduled' ,
            Schema::PREVIOUS_START_DATE => '2026-09-01T09:30:00+02:00' ,
            Schema::START_DATE          => '2026-09-03T09:30:00+02:00' ,
            Oihana::APPOINTMENT_STATUS  => AppointmentStatus::PLANNED ,
        ]);

        $this->assertSame( 'https://schema.org/EventRescheduled' , $appointment->eventStatus       );
        $this->assertSame( AppointmentStatus::PLANNED            , $appointment->appointmentStatus );
        $this->assertSame( '2026-09-01T09:30:00+02:00'           , $appointment->previousStartDate );
    }

    /**
     * A customer not on the books yet is a name and a telephone number, with no key
     * of its own — the property has to hold it as readily as a known one.
     * @return void
     * @throws ReflectionException
     */
    public function testTheCustomerMayBeFreeForm(): void
    {
        $appointment = new CustomerAppointment
        ([
            Schema::CUSTOMER => [ Schema::NAME => 'Acme Corporation' , Schema::TELEPHONE => '05 56 00 00 00' ] ,
        ]);

        $this->assertIsArray( $appointment->customer );
        $this->assertSame( 'Acme Corporation' , $appointment->customer[ Schema::NAME ] );
    }

    /**
     * Every nested slot is read back as the class that names it — the whole reason
     * the attributes are declared.
     *
     * @throws ReflectionException
     */
    public function testReflectionReadsEachSlotAsItsOwnClass(): void
    {
        $appointment = new Reflection()->hydrate
        (
            [
                Schema::ORGANIZER => [ Schema::ID => 'JDOE' , Schema::NAME => 'Jane Doe' ] ,
                Schema::CUSTOMER  => [ Schema::AT_TYPE => Customer::getSchemaType() , Schema::ID => '100200' , Schema::NAME => 'Acme Corporation' ] ,
                Schema::ATTENDEE  => [ [ Schema::NAME => 'Alice Smith' ] ] ,
                Schema::LOCATION  => [ Schema::AT_TYPE => CustomerSite::getSchemaType() , Schema::NAME => 'Head office' ] ,
                Oihana::REPORT    => [ Schema::TEXT => 'A quotation is expected.' ] ,
            ],
            CustomerAppointment::class
        );

        $this->assertInstanceOf( Seller::class           , $appointment->organizer      );
        $this->assertInstanceOf( Customer::class         , $appointment->customer       );
        $this->assertInstanceOf( CustomerEmployee::class , $appointment->attendee[ 0 ]  );
        $this->assertInstanceOf( CustomerSite::class     , $appointment->location       );
        $this->assertInstanceOf( VisitReport::class      , $appointment->report         );
        $this->assertSame( 'A quotation is expected.'          , $appointment->report->text   );
    }

    /**
     * What the salesperson means to show is wrapped in offers, one per product :
     * the wrapper is what carries the intention beside the reference.
     *
     * The class the offered thing is read back as is {@see Offer::$itemOffered}'s
     * own business — a union of four, resolved by the consumer — so what is checked
     * here is the wrapping and what it carries.
     *
     * @throws ReflectionException
     */
    public function testMakesOfferWrapsEachProduct(): void
    {
        $appointment = new Reflection()->hydrate
        (
            [
                Schema::MAKES_OFFER =>
                [
                    [
                        Schema::DESCRIPTION  => 'Show the Model A rather than the Model B.' ,
                        Schema::ITEM_OFFERED => [ Schema::AT_TYPE => Product::getSchemaType() , Schema::ID => '500100' , Schema::NAME => 'Model A widget' ] ,
                    ],
                ],
            ],
            CustomerAppointment::class
        );

        $this->assertIsArray( $appointment->makesOffer );
        $this->assertInstanceOf( Offer::class , $appointment->makesOffer[ 0 ] );
        $this->assertSame( 'Show the Model A rather than the Model B.' , $appointment->makesOffer[ 0 ]->description );
        $this->assertSame( 'Model A widget' , $appointment->makesOffer[ 0 ]->itemOffered->name );
    }

    /**
     * The salesperson whose diary holds the meeting and the one the customer is
     * attached to are two questions : an assistant's booking answers only the first.
     * @return void
     * @throws ReflectionException
     */
    public function testTheOrganizerIsNotTheAssignedSeller(): void
    {
        $appointment = new CustomerAppointment
        ([
            Schema::ORGANIZER        => new Seller([ Schema::ID => 'JDOE' ] ) ,
            Oihana::ASSIGNED_SELLER  => 'RROE' ,
        ]);

        $this->assertSame( 'JDOE' , $appointment->organizer->id  );
        $this->assertSame( 'RROE' , $appointment->assignedSeller );
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testWhatIsNotSetIsNotSerialized(): void
    {
        $json = new CustomerAppointment([ Schema::NAME => 'Phone call' ] )->jsonSerialize() ;

        $this->assertSame( 'Phone call' , $json[ Schema::NAME ] ?? null );
        $this->assertArrayNotHasKey( Oihana::REPORT , $json );
        $this->assertArrayNotHasKey( Oihana::TAGS   , $json );
    }
}
