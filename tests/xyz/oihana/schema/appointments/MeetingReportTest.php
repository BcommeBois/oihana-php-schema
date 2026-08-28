<?php

namespace tests\xyz\oihana\schema\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\constants\Schema;
use org\schema\CreativeWork;
use org\schema\Thing;

use xyz\oihana\schema\appointments\FollowUp;
use xyz\oihana\schema\appointments\MeetingReport;
use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\thesaurus\ThesaurusTerm;

use function xyz\oihana\schema\helpers\hydrate\appointments\hydrateMeetingReport;

class MeetingReportTest extends TestCase
{
    public function testIsACreativeWork(): void
    {
        $report = new MeetingReport() ;

        $this->assertInstanceOf( CreativeWork::class , $report );
        $this->assertInstanceOf( Thing::class        , $report );
    }

    public function testSchemaType(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/MeetingReport' , MeetingReport::getSchemaType() );
    }

    public function testPropertiesDefaultToNull(): void
    {
        $report = new MeetingReport() ;

        $this->assertNull( $report->attendee ?? null );
        $this->assertNull( $report->followUp ?? null );
        $this->assertNull( $report->tags     ?? null );
        $this->assertNull( $report->topics   ?? null );
    }

    public function testConstructorCopiesTheProperties(): void
    {
        $report = new MeetingReport
        ([
            Schema::TEXT   => 'Everything the boxes cannot hold.' ,
            Oihana::TOPICS => [ 'ROADMAP' , 'BUDGET' ] ,
        ]);

        $this->assertSame( 'Everything the boxes cannot hold.' , $report->text   );
        $this->assertSame( [ 'ROADMAP' , 'BUDGET' ]            , $report->topics );
    }

    /**
     * What every report carries is resolved here — the promises and the vocabularies —
     * so that a family reusing this helper never copies its body.
     *
     * @throws ReflectionException
     */
    public function testItResolvesWhatEveryReportCarries(): void
    {
        $report = hydrateMeetingReport
        ([
            Oihana::FOLLOW_UP => [ [ Schema::DESCRIPTION => 'Send the summary' ] ] ,
            Oihana::TOPICS    => [ [ Schema::ID => 'ROADMAP' ] ] ,
        ]);

        $this->assertInstanceOf( MeetingReport::class , $report );
        $this->assertInstanceOf( FollowUp::class      , $report->followUp[ 0 ] );
        $this->assertInstanceOf( ThesaurusTerm::class , $report->topics[ 0 ]   );
    }

    /**
     * ⚠️ Who sits at a table depends on the kind of meeting, so this helper leaves the
     * attendees exactly as it found them : the family that knows resolves them after it.
     *
     * @throws ReflectionException
     */
    public function testItLeavesTheAttendeesToWhoeverKnows(): void
    {
        $report = hydrateMeetingReport([ Schema::ATTENDEE => [ [ Schema::NAME => 'Alice Smith' ] ] ]) ;

        $this->assertIsArray( $report->attendee );
        $this->assertIsArray( $report->attendee[ 0 ] , 'the raw row is left as it stands' );
    }

    /**
     * 🔑 The class to build is a parameter, which is what lets a subclass reuse the whole
     * body rather than copy it.
     *
     * @throws ReflectionException
     */
    public function testItBuildsTheClassItIsAskedFor(): void
    {
        $report = hydrateMeetingReport( [ Schema::TEXT => 'Nothing to report.' ] , ThesaurusTerm::class , VisitReport::class ) ;

        $this->assertInstanceOf( VisitReport::class , $report );
    }

    /**
     * A list of reports comes back as a list, and an unresolved handle inside it survives
     * as it stands rather than being dropped.
     *
     * @throws ReflectionException
     */
    public function testItHydratesAListAndKeepsBareReferences(): void
    {
        $reports = hydrateMeetingReport
        ([
            [ Schema::TEXT => 'The first one.' ] ,
            'a-bare-reference' ,
        ]);

        $this->assertCount( 2 , $reports );
        $this->assertInstanceOf( MeetingReport::class , $reports[ 0 ] );
        $this->assertSame( 'a-bare-reference' , $reports[ 1 ] );
    }

    public function testItSerializesWhatItWasGiven(): void
    {
        $json = new MeetingReport([ Schema::TEXT => 'Budget agreed.' ] )->jsonSerialize() ;

        $this->assertSame( 'Budget agreed.' , $json[ Schema::TEXT ] ?? null );
    }
}
