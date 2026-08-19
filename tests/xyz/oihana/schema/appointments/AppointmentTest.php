<?php

namespace tests\xyz\oihana\schema\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;
use org\schema\Event;
use org\schema\Offer;
use org\schema\Product;
use org\schema\Thing;

use xyz\oihana\schema\appointments\Appointment;
use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\AppointmentStatus;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\people\Seller;
use xyz\oihana\schema\places\CustomerSite;

class AppointmentTest extends TestCase
{
    public function testIsAnEvent(): void
    {
        $appointment = new Appointment() ;
        $this->assertInstanceOf( Event::class , $appointment );
    }

    public function testSchemaType(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/Appointment' , Appointment::getSchemaType() );
    }

    public function testPropertiesDefaultToNull(): void
    {
        $appointment = new Appointment() ;

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
        $appointment = new Appointment
        ([
            Schema::NAME                => 'Point gamme terrasse' ,
            Schema::START_DATE          => '2026-09-03T09:30:00+02:00' ,
            Schema::END_DATE            => '2026-09-03T10:30:00+02:00' ,
            Oihana::APPOINTMENT_TYPE    => 'VISIT' ,
            Oihana::APPOINTMENT_STATUS  => AppointmentStatus::PLANNED ,
            Oihana::TAGS                => [ 'MEAL' , 'JOBSITE' ] ,
        ]);

        $this->assertSame( 'Point gamme terrasse'                          , $appointment->name              );
        $this->assertSame( '2026-09-03T09:30:00+02:00'                     , $appointment->startDate         );
        $this->assertSame( '2026-09-03T10:30:00+02:00'                     , $appointment->endDate           );
        $this->assertSame( 'VISIT'                                          , $appointment->appointmentType   );
        $this->assertSame( AppointmentStatus::PLANNED                       , $appointment->appointmentStatus );
        $this->assertSame( [ 'MEAL' , 'JOBSITE' ]                           , $appointment->tags              );
    }

    /**
     * The two axes are written side by side and neither replaces the other : the
     * slot moved, the meeting has still to happen.
     * @return void
     * @throws ReflectionException
     */
    public function testTheSlotStatusAndTheMeetingStatusAreIndependent(): void
    {
        $appointment = new Appointment
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
        $appointment = new Appointment
        ([
            Schema::CUSTOMER => [ Schema::NAME => 'Charpentes du Sud' , Schema::TELEPHONE => '05 56 00 00 00' ] ,
        ]);

        $this->assertIsArray( $appointment->customer );
        $this->assertSame( 'Charpentes du Sud' , $appointment->customer[ Schema::NAME ] );
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
                Schema::ORGANIZER => [ Schema::ID => 'ALPER' , Schema::NAME => 'A. Perez' ] ,
                Schema::CUSTOMER  => [ Schema::AT_TYPE => Customer::getSchemaType() , Schema::ID => '741278' , Schema::NAME => 'Charpentes du Sud' ] ,
                Schema::ATTENDEE  => [ [ Schema::NAME => 'Claire Martin' ] ] ,
                Schema::LOCATION  => [ Schema::AT_TYPE => CustomerSite::getSchemaType() , Schema::NAME => 'Dépôt de Mérignac' ] ,
                Oihana::REPORT    => [ Schema::TEXT => 'Chiffrage attendu.' ] ,
            ],
            Appointment::class
        );

        $this->assertInstanceOf( Seller::class           , $appointment->organizer      );
        $this->assertInstanceOf( Customer::class         , $appointment->customer       );
        $this->assertInstanceOf( CustomerEmployee::class , $appointment->attendee[ 0 ]  );
        $this->assertInstanceOf( CustomerSite::class     , $appointment->location       );
        $this->assertInstanceOf( VisitReport::class      , $appointment->report         );
        $this->assertSame( 'Chiffrage attendu.'          , $appointment->report->text   );
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
                        Schema::DESCRIPTION  => 'Lui montrer la 21 mm plutôt que la 25.' ,
                        Schema::ITEM_OFFERED => [ Schema::AT_TYPE => Product::getSchemaType() , Schema::ID => '105997' , Schema::NAME => 'Lame composite 21 mm' ] ,
                    ],
                ],
            ],
            Appointment::class
        );

        $this->assertIsArray( $appointment->makesOffer );
        $this->assertInstanceOf( Offer::class , $appointment->makesOffer[ 0 ] );
        $this->assertSame( 'Lui montrer la 21 mm plutôt que la 25.' , $appointment->makesOffer[ 0 ]->description );
        $this->assertSame( 'Lame composite 21 mm' , $appointment->makesOffer[ 0 ]->itemOffered->name );
    }

    /**
     * The salesperson whose diary holds the meeting and the one the customer is
     * attached to are two questions : an assistant's booking answers only the first.
     * @return void
     * @throws ReflectionException
     */
    public function testTheOrganizerIsNotTheAssignedSeller(): void
    {
        $appointment = new Appointment
        ([
            Schema::ORGANIZER        => new Seller([ Schema::ID => 'ALPER' ] ) ,
            Oihana::ASSIGNED_SELLER  => 'MADEL' ,
        ]);

        $this->assertSame( 'ALPER' , $appointment->organizer->id  );
        $this->assertSame( 'MADEL' , $appointment->assignedSeller );
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testWhatIsNotSetIsNotSerialized(): void
    {
        $json = new Appointment([ Schema::NAME => 'Rappel téléphonique' ] )->jsonSerialize() ;

        $this->assertSame( 'Rappel téléphonique' , $json[ Schema::NAME ] ?? null );
        $this->assertArrayNotHasKey( Oihana::REPORT , $json );
        $this->assertArrayNotHasKey( Oihana::TAGS   , $json );
    }
}
