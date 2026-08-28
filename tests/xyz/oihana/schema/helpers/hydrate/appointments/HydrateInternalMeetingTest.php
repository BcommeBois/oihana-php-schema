<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;

use xyz\oihana\schema\appointments\InternalMeeting;
use xyz\oihana\schema\appointments\MeetingReport;
use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\thesaurus\ThesaurusTerm;

use function xyz\oihana\schema\helpers\hydrate\appointments\hydrateInternalMeeting;

class HydrateInternalMeetingTest extends TestCase
{
    /**
     * Anything that is not an array is left untouched — an unresolved handle stays a
     * handle.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItLeavesWhatIsNotAnArrayAlone(): void
    {
        $this->assertSame( 'a-bare-reference' , hydrateInternalMeeting( 'a-bare-reference' ) );
        $this->assertNull( hydrateInternalMeeting() );
    }

    /**
     * ⚠️ A meeting between colleagues has no counterpart, and that is the definition
     * of the family rather than an omission.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItIsWithNobodyOutside(): void
    {
        $meeting = hydrateInternalMeeting
        ([
            Schema::NAME       => 'Weekly review' ,
            Schema::START_DATE => '2035-09-01T09:00:00+02:00' ,
        ]);

        $this->assertInstanceOf( InternalMeeting::class , $meeting );
        $this->assertNull( $meeting->about ?? null );
    }

    /**
     * 🔑 Nobody outside is invited, so the union the parent leaves wide narrows to the
     * accounts that hold a diary here.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItsAttendeesAreAccounts(): void
    {
        $meeting = hydrateInternalMeeting
        ([
            Schema::ATTENDEE => [ [ Schema::GIVEN_NAME => 'Alice' , Schema::FAMILY_NAME => 'Smith' ] ] ,
        ]);

        $this->assertIsArray( $meeting->attendee );
        $this->assertInstanceOf( User::class , $meeting->attendee[ 0 ] );
        $this->assertSame( 'Alice' , $meeting->attendee[ 0 ]->givenName );
    }

    /**
     * One attendee sent on its own is read like a list of one : a diary that invites a
     * single colleague gets the same shape back as one that invites four.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testASingleAttendeeIsReadLikeAListOfOne(): void
    {
        $meeting = hydrateInternalMeeting([ Schema::ATTENDEE => [ Schema::GIVEN_NAME => 'Alice' ] ]) ;

        $this->assertIsArray( $meeting->attendee );
        $this->assertInstanceOf( User::class , $meeting->attendee[ 0 ] );
    }

    /**
     * A bare handle among the attendees survives as it stands ; only an entry that WAS
     * an array and gave nothing is dropped.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testABareAttendeeSurvives(): void
    {
        $meeting = hydrateInternalMeeting([ Schema::ATTENDEE => [ 'an-account-handle' , [ Schema::GIVEN_NAME => 'Alice' ] ] ]) ;

        $this->assertCount( 2 , $meeting->attendee );
        $this->assertSame( 'an-account-handle' , $meeting->attendee[ 0 ] );
        $this->assertInstanceOf( User::class , $meeting->attendee[ 1 ] );
    }

    /**
     * An empty guest list resolves to nothing rather than to an empty list : there is
     * no « nobody was invited » to serve, only an absence.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testAnEmptyGuestListAnswersNothing(): void
    {
        $meeting = hydrateInternalMeeting([ Schema::ATTENDEE => [] ]) ;

        $this->assertNull( $meeting->attendee );
    }

    /**
     * A meeting between colleagues brings back no mood and no outcome to publish : its
     * write-up is the common report, resolved to its depth.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItsReportIsTheCommonOne(): void
    {
        $meeting = hydrateInternalMeeting
        ([
            Oihana::REPORT => [ Schema::TEXT => 'Budget agreed.' , Oihana::FOLLOW_UP => [ [ Schema::DESCRIPTION => 'Send the summary' ] ] ] ,
        ]);

        $this->assertInstanceOf( MeetingReport::class , $meeting->report );
        $this->assertSame( 'Budget agreed.' , $meeting->report->text );
        $this->assertCount( 1 , $meeting->report->followUp );
    }

    /**
     * The term map may name a branch for the report, exactly as it does on a customer
     * meeting.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testTheReportMayCarryItsOwnTermBranch(): void
    {
        $meeting = hydrateInternalMeeting
        (
            [ Oihana::REPORT => [ Oihana::TOPICS => [ [ Schema::ID => 'ROADMAP' ] ] ] ] ,
            [ Oihana::REPORT => [ Oihana::TOPICS => ThesaurusTerm::class ] ] ,
        );

        $this->assertInstanceOf( MeetingReport::class , $meeting->report );
        $this->assertCount( 1 , $meeting->report->topics );
    }

    /**
     * A list of meetings comes back as a list, and an unresolved handle inside it
     * survives as it stands.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testItHydratesAListAndKeepsBareReferences(): void
    {
        $meetings = hydrateInternalMeeting
        ([
            [ Schema::NAME => 'Weekly review' ] ,
            'a-bare-reference' ,
        ]);

        $this->assertCount( 2 , $meetings );
        $this->assertInstanceOf( InternalMeeting::class , $meetings[ 0 ] );
        $this->assertSame( 'a-bare-reference' , $meetings[ 1 ] );
    }

    /**
     * A list that resolves to nothing answers nothing, rather than an empty list.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testAListThatResolvesToNothingAnswersNull(): void
    {
        $this->assertNull( hydrateInternalMeeting( [ [] ] ) );
    }
}
