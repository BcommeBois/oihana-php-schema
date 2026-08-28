<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;
use org\schema\DefinedTerm;

use xyz\oihana\schema\appointments\Appointment;
use xyz\oihana\schema\appointments\InternalMeeting;
use xyz\oihana\schema\appointments\MeetingReport;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\AppointmentStatus;

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
     * ⚠️ Whom a meeting is with and who may be invited to it are what tell one family
     * from another : this helper cannot know, so it leaves the two exactly as it found
     * them rather than reading them back as the wrong kind.
     *
     * The write-up is the one exception, and it is the attribute's doing rather than
     * this helper's : it comes back typed but **shallow**, and each family resolves what
     * is inside it with its own report helper.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItLeavesToEachFamilyWhatOnlyItKnows(): void
    {
        $appointment = hydrateAppointment
        ([
            Schema::ABOUT    => [ Schema::NAME => 'Acme Corporation' ] ,
            Schema::ATTENDEE => [ [ Schema::NAME => 'Jane Doe' ] ] ,
            Oihana::REPORT   => [ Schema::TEXT => 'Everything the boxes cannot hold.' , Oihana::FOLLOW_UP => [ [ Schema::DESCRIPTION => 'Send the summary' ] ] ] ,
        ]);

        $this->assertIsArray( $appointment->about    , 'the counterpart is left as it stands' );
        $this->assertIsArray( $appointment->attendee , 'and so are the people expected' );

        $this->assertInstanceOf( MeetingReport::class , $appointment->report );
        $this->assertIsArray( $appointment->report->followUp , 'typed, but shallow : what is inside is the family helper business' );
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
        $meeting = hydrateAppointment( [ Schema::NAME => 'Weekly review' ] , DefinedTerm::class , InternalMeeting::class ) ;

        $this->assertInstanceOf( InternalMeeting::class , $meeting );
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
