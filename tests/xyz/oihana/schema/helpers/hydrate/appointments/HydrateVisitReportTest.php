<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\constants\Schema;
use org\schema\DefinedTerm;
use org\schema\Organization;
use org\schema\Person;

use xyz\oihana\schema\appointments\FollowUp;
use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\people\CustomerEmployee;

use function xyz\oihana\schema\helpers\hydrate\appointments\hydrateFollowUp;
use function xyz\oihana\schema\helpers\hydrate\appointments\hydrateVisitReport;

final class HydrateVisitReportTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesASingleReportWithItsNestedReferences(): void
    {
        $report = hydrateVisitReport
        ([
            'text'     => 'Everything the boxes cannot hold.' ,
            'mood'     => [ 'id' => 'SATISFIED' , 'name' => 'Satisfied' ] ,
            'outcome'  => [ 'id' => 'QUOTE_TO_WRITE' ] ,
            'tags'     => [ [ 'id' => 'FIRST_VISIT' ] ] ,
            'topics'   => [ [ 'id' => 'PRICING' ] , [ 'id' => 'DELIVERY' ] ] ,
            'attendee' => [ [ 'name' => 'Jane Doe' , 'jobTitle' => [ 'id' => 'BUYER' ] ] ] ,
            'author'   => [ Schema::AT_TYPE => 'Person' , 'name' => 'Richard Roe' ] ,
            'followUp' => [ [ 'followUpType' => [ 'id' => 'CALL_BACK' ] ] ] ,
        ]) ;

        $this->assertInstanceOf( VisitReport::class , $report ) ;
        $this->assertInstanceOf( DefinedTerm::class , $report->mood ) ;
        $this->assertInstanceOf( DefinedTerm::class , $report->outcome ) ;
        $this->assertInstanceOf( Person::class      , $report->author ) ;

        $this->assertContainsOnlyInstancesOf( DefinedTerm::class      , $report->tags ) ;
        $this->assertContainsOnlyInstancesOf( DefinedTerm::class      , $report->topics ) ;
        $this->assertContainsOnlyInstancesOf( CustomerEmployee::class , $report->attendee ) ;
        $this->assertContainsOnlyInstancesOf( FollowUp::class         , $report->followUp ) ;

        // The contacts met carry their own references typed, not one level short.
        $this->assertInstanceOf( DefinedTerm::class , $report->attendee[ 0 ]->jobTitle ) ;
    }

    /**
     * The report is what a consumer reads through the constructor : the `Organization|Person`
     * union of `author` is settled by the payload's `@type`, which no property type can do.
     *
     * @throws ReflectionException
     */
    public function testTheAuthorMayBeAnOrganization(): void
    {
        $report = hydrateVisitReport( [ 'author' => [ 'name' => 'Acme Corporation' ] ] ) ;

        $this->assertInstanceOf( Organization::class , $report->author ) ;
    }

    /**
     * 🔑 « This report has no follow-up » is an answer, and a reader walking the value
     * deserves a list to walk. The empty list is written back as it came, where every
     * other nested reference answers `null`.
     *
     * @throws ReflectionException
     */
    public function testAnEmptyFollowUpListStaysAnEmptyList(): void
    {
        $report = hydrateVisitReport( [ 'mood' => [] , 'followUp' => [] , 'attendee' => [] ] ) ;

        $this->assertSame( [] , $report->followUp ) ;
        $this->assertNull( $report->mood ) ;
        $this->assertNull( $report->attendee ) ;

        // Same answer through the report as through the nested helper on its own.
        $this->assertSame( hydrateFollowUp( [] ) , $report->followUp ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $reports = hydrateVisitReport
        ([
            [ 'mood' => [ 'id' => 'SATISFIED' ] ] ,
            [ 'mood' => [ 'id' => 'NEUTRAL'   ] ] ,
        ]) ;

        $this->assertIsArray( $reports ) ;
        $this->assertCount( 2 , $reports ) ;
        $this->assertContainsOnlyInstancesOf( VisitReport::class , $reports ) ;

        $this->assertNull( hydrateVisitReport( [] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateVisitReport() ) ;
        $this->assertSame( 'report-ref-42' , hydrateVisitReport( 'report-ref-42' ) ) ;

        $report = new VisitReport() ;
        $this->assertSame( $report , hydrateVisitReport( $report ) ) ;
    }

    /**
     * A term nobody resolved yet stays the code it was.
     *
     * @throws ReflectionException
     */
    public function testLeavesUnresolvedReferencesUntouched(): void
    {
        $report = hydrateVisitReport( [ 'mood' => 'SATISFIED' , 'author' => 'seller-ref-42' ] ) ;

        $this->assertSame( 'SATISFIED'     , $report->mood ) ;
        $this->assertSame( 'seller-ref-42' , $report->author ) ;
    }

    /**
     * The case the helper exists for : a report read back through the constructor
     * carries follow-ups that are objects, each with the meeting it produced.
     *
     * @throws ReflectionException
     */
    public function testTheFollowUpsCarryTheMeetingTheyProduced(): void
    {
        $report = hydrateVisitReport
        ([
            'followUp' =>
            [
                [
                    'followUpType' => [ 'id' => 'VISIT_AGAIN' ] ,
                    'result'       => [ 'name' => 'Second meeting with Acme Corporation' ] ,
                ] ,
            ] ,
        ]) ;

        $followUp = $report->followUp[ 0 ] ;

        $this->assertInstanceOf( FollowUp::class , $followUp ) ;
        $this->assertInstanceOf( DefinedTerm::class , $followUp->followUpType ) ;
        $this->assertSame( 'Second meeting with Acme Corporation' , $followUp->result->name ) ;
    }

    /**
     * 🔑 **A bare reference survives inside a list**, exactly as it does on its own — the
     * contract every helper of the family states in its header, applied entry by entry.
     * A property that stores handles rather than resolved objects used to read back `null`.
     *
     * The keys matter as much as the contents : a filtered list left with gaps serializes
     * as a JSON **object**, and a consumer walking the value gets something it cannot walk.
     *
     * @throws ReflectionException
     */
    public function testAListOfReferencesSurvivesAndKeepsItsKeys(): void
    {
        $bare = hydrateVisitReport( [ 'report-ref-42' , 'report-ref-42' ] ) ;

        $this->assertSame( [ 'report-ref-42' , 'report-ref-42' ] , $bare ) ;

        $mixed = hydrateVisitReport( [ 'report-ref-42' , [ 'mood' => [ 'id' => 'SATISFIED' ] ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 'report-ref-42' , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( VisitReport::class , $mixed[ 1 ] ) ;
    }
}
