<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;
use org\schema\DefinedTerm;

use org\schema\Offer;
use org\schema\Organization;
use org\schema\Person;

use xyz\oihana\schema\appointments\Appointment;
use xyz\oihana\schema\appointments\FollowUp;
use xyz\oihana\schema\appointments\MeetingReport;
use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\AppointmentStatus;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\products\Product;

use function xyz\oihana\schema\helpers\hydrate\appointments\hydrateAppointment;

class HydrateAppointmentTest extends TestCase
{
    /**
     * What every meeting carries is resolved here, so that a family reusing this
     * helper never copies its body.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItResolvesWhatEveryMeetingCarries(): void
    {
        $appointment = hydrateAppointment
        ([
            Schema::EVENT_STATUS       => 'https://schema.org/EventScheduled' ,
            Oihana::APPOINTMENT_STATUS => [ Schema::AT_TYPE => AppointmentStatus::getSchemaType() , Schema::ID => 'https://schema.oihana.xyz/AppointmentDone' ] ,
            Oihana::APPOINTMENT_TYPE   => [ Schema::ID => 'CALL' ] ,
            Oihana::TAGS               => [ [ Schema::ID => 'MEAL' ] ] ,
        ]);

        $this->assertInstanceOf( Appointment::class  , $appointment );
        $this->assertInstanceOf( DefinedTerm::class  , $appointment->appointmentType );
        $this->assertInstanceOf( DefinedTerm::class  , $appointment->tags[ 0 ] );
    }

    /**
     * 🔑 The counterpart resolves on the type its stored copy carries : a value
     * announcing a customer is read back as one, and nothing else ever is.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItResolvesTheCounterpartOnItsStoredType(): void
    {
        $appointment = hydrateAppointment
        ([
            Schema::ABOUT => [ Schema::ADDITIONAL_TYPE => Customer::getSchemaType() , Schema::NAME => 'Acme Corporation' ] ,
        ]);

        $this->assertInstanceOf( Customer::class , $appointment->about );
        $this->assertSame( 'Acme Corporation' , $appointment->about->name );
    }

    /**
     * A counterpart with no stored type falls back on its `@type` : an organization
     * unless it claims to be a person — and a bare reference survives as it stands.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testACounterpartWithoutAStoredTypeFallsBackOnItsAtType(): void
    {
        $organization = hydrateAppointment([ Schema::ABOUT => [ Schema::NAME => 'Acme Corporation' ] ]);

        $this->assertInstanceOf( Organization::class , $organization->about );
        $this->assertNotInstanceOf( Customer::class , $organization->about );

        $person = hydrateAppointment([ Schema::ABOUT => [ Schema::AT_TYPE => 'Person' , Schema::NAME => 'Jane Doe' ] ]);

        $this->assertInstanceOf( Person::class , $person->about );

        $reference = hydrateAppointment([ Schema::ABOUT => 'a-bare-reference' ]);

        $this->assertSame( 'a-bare-reference' , $reference->about );
    }

    /**
     * 🔑 One table may seat an account of the house, a contact of the counterpart and a
     * plain person together : each entry resolves on its own stored type, and a bare
     * reference survives as it stands.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItSeatsAccountsAndContactsAroundOneTable(): void
    {
        $appointment = hydrateAppointment
        ([
            Schema::ATTENDEE =>
            [
                [ Schema::ADDITIONAL_TYPE => User::getSchemaType() , Schema::NAME => 'Alice Smith' ] ,
                [ Schema::ADDITIONAL_TYPE => CustomerEmployee::getSchemaType() , Schema::NAME => 'Jane Doe' ] ,
                [ Schema::NAME => 'John Roe' ] ,
                [ Schema::AT_TYPE => 'Organization' , Schema::NAME => 'Acme Corporation' ] ,
                'a-bare-reference' ,
            ] ,
        ]);

        $this->assertInstanceOf( User::class , $appointment->attendee[ 0 ] );
        $this->assertInstanceOf( CustomerEmployee::class , $appointment->attendee[ 1 ] );

        $this->assertInstanceOf( Person::class , $appointment->attendee[ 2 ] );
        $this->assertNotInstanceOf( User::class , $appointment->attendee[ 2 ] );
        $this->assertNotInstanceOf( CustomerEmployee::class , $appointment->attendee[ 2 ] );

        $this->assertInstanceOf( Organization::class , $appointment->attendee[ 3 ] );
        $this->assertSame( 'a-bare-reference' , $appointment->attendee[ 4 ] );
    }

    /**
     * A single attendee, not wrapped in a list, comes back alone.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testASingleAttendeeComesBackAlone(): void
    {
        $appointment = hydrateAppointment
        ([
            Schema::ATTENDEE => [ Schema::AT_TYPE => 'Person' , Schema::NAME => 'Jane Doe' ] ,
        ]);

        $this->assertInstanceOf( Person::class , $appointment->attendee );
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItResolvesTheOffersOneMeansToPresent(): void
    {
        $appointment = hydrateAppointment
        ([
            Schema::MAKES_OFFER => [ [ Schema::DESCRIPTION => 'Show the Model A.' , Schema::ITEM_OFFERED => [ Schema::NAME => 'Model A widget' ] ] ] ,
        ]);

        $this->assertContainsOnlyInstancesOf( Offer::class , $appointment->makesOffer );
        $this->assertInstanceOf( Product::class , $appointment->makesOffer[ 0 ]->itemOffered );
    }

    /**
     * 🔑 The report resolves on ITS stored type — a visit's write-up comes back with its
     * richer class, any other with the common one — and in depth either way : the
     * promises inside are typed, where the attribute alone left them shallow.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItResolvesTheReportOnItsStoredType(): void
    {
        $visit = hydrateAppointment
        ([
            Oihana::REPORT =>
            [
                Schema::ADDITIONAL_TYPE => VisitReport::getSchemaType() ,
                Schema::TEXT            => 'Went well.' ,
                Oihana::FOLLOW_UP       => [ [ Schema::DESCRIPTION => 'Send the summary' ] ] ,
            ] ,
        ]);

        $this->assertInstanceOf( VisitReport::class , $visit->report );
        $this->assertContainsOnlyInstancesOf( FollowUp::class , $visit->report->followUp );

        $common = hydrateAppointment
        ([
            Oihana::REPORT => [ Schema::TEXT => 'Noted.' , Oihana::FOLLOW_UP => [ [ Schema::DESCRIPTION => 'Book the room' ] ] ] ,
        ]);

        $this->assertInstanceOf( MeetingReport::class , $common->report );
        $this->assertNotInstanceOf( VisitReport::class , $common->report );
        $this->assertContainsOnlyInstancesOf( FollowUp::class , $common->report->followUp );
    }

    /**
     * 🔑 The class to build is a parameter, which is what lets a family reuse the
     * whole body rather than copy it.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItBuildsTheClassItIsAskedFor(): void
    {
        $meeting = hydrateAppointment( [ Schema::NAME => 'Weekly review' ] , DefinedTerm::class , SampleMeeting::class ) ;

        $this->assertInstanceOf( SampleMeeting::class , $meeting );
    }

    /**
     * A list of meetings comes back as a list, and an unresolved handle inside it
     * survives as it stands rather than being dropped.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItHydratesAListAndKeepsBareReferences(): void
    {
        $appointments = hydrateAppointment
        ([
            [ Schema::NAME => 'The first one.' ] ,
            'a-bare-reference' ,
        ]);

        $this->assertCount( 2 , $appointments );
        $this->assertInstanceOf( Appointment::class , $appointments[ 0 ] );
        $this->assertSame( 'a-bare-reference' , $appointments[ 1 ] );
    }
}

/**
 * A subclass a consumer may declare on its own — what the class parameter must build.
 */
class SampleMeeting extends Appointment {}
