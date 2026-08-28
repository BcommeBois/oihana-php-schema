<?php

namespace tests\xyz\oihana\schema\appointments ;

use PHPUnit\Framework\TestCase;

use org\schema\constants\Schema;
use org\schema\Event;
use org\schema\Thing;

use xyz\oihana\schema\appointments\Appointment;
use xyz\oihana\schema\constants\Oihana;

class AppointmentTest extends TestCase
{
    public function testIsAnEvent(): void
    {
        $appointment = new Appointment() ;

        $this->assertInstanceOf( Event::class , $appointment );
        $this->assertInstanceOf( Thing::class , $appointment );
    }

    public function testSchemaType(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/Appointment' , Appointment::getSchemaType() );
    }

    public function testPropertiesDefaultToNull(): void
    {
        $appointment = new Appointment() ;

        $this->assertNull( $appointment->about             ?? null );
        $this->assertNull( $appointment->appointmentStatus ?? null );
        $this->assertNull( $appointment->appointmentType   ?? null );
        $this->assertNull( $appointment->assignedCompany   ?? null );
        $this->assertNull( $appointment->attendee          ?? null );
        $this->assertNull( $appointment->organizer         ?? null );
        $this->assertNull( $appointment->report            ?? null );
        $this->assertNull( $appointment->tags              ?? null );
    }

    /**
     * A meeting requires a moment and a diary ; whether it requires somebody on the
     * other side is the family's business, not this class's.
     */
    public function testAMeetingMayBeWithNobodyOutside(): void
    {
        $appointment = new Appointment
        ([
            Schema::NAME       => 'Weekly review' ,
            Schema::START_DATE => '2035-09-01T09:00:00+02:00' ,
        ]);

        $this->assertSame( 'Weekly review' , $appointment->name );
        $this->assertNull( $appointment->about ?? null );
    }

    /**
     * 🔑 A counterpart travels as a raw row before anything hydrates it, so the
     * property has to carry an array — which is what every other reference of the
     * library already reads like.
     */
    public function testTheCounterpartAcceptsARawRow(): void
    {
        $appointment = new Appointment([ Schema::ABOUT => [ Schema::NAME => 'Acme Corporation' ] ] );

        $this->assertIsArray( $appointment->about );
        $this->assertSame( 'Acme Corporation' , $appointment->about[ Schema::NAME ] );
    }

    /**
     * 🔑 The two axes of state are not interchangeable : one says what became of the
     * slot, the other what became of the meeting. Moving one leaves the other alone.
     */
    public function testTheTwoAxesOfStateAreIndependent(): void
    {
        $appointment = new Appointment
        ([
            Schema::EVENT_STATUS      => 'https://schema.org/EventScheduled' ,
            Oihana::APPOINTMENT_STATUS => 'https://schema.oihana.xyz/AppointmentDone' ,
        ]);

        $this->assertSame( 'https://schema.org/EventScheduled'           , $appointment->eventStatus );
        $this->assertSame( 'https://schema.oihana.xyz/AppointmentDone'   , $appointment->appointmentStatus );
    }
}
