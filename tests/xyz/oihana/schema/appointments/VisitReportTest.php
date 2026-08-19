<?php

namespace tests\xyz\oihana\schema\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;
use org\schema\CreativeWork;
use org\schema\Thing;

use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\people\Seller;

class VisitReportTest extends TestCase
{
    public function testIsACreativeWork(): void
    {
        $report = new VisitReport() ;

        $this->assertInstanceOf( CreativeWork::class , $report );
        $this->assertInstanceOf( Thing::class        , $report );
    }

    public function testSchemaType(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/VisitReport' , VisitReport::getSchemaType() );
    }

    public function testPropertiesDefaultToNull(): void
    {
        $report = new VisitReport() ;

        $this->assertNull( $report->attendee ?? null );
        $this->assertNull( $report->mood     ?? null );
        $this->assertNull( $report->outcome  ?? null );
        $this->assertNull( $report->tags     ?? null );
        $this->assertNull( $report->topics   ?? null );
    }

    public function testConstructorCopiesTheProperties(): void
    {
        $report = new VisitReport
        ([
            Schema::TEXT     => 'Gamme bien reçue, chiffrage attendu.' ,
            Oihana::OUTCOME  => 'QUOTE' ,
            Oihana::MOOD     => 'GREEN' ,
            Oihana::TOPICS   => [ 'PRICING' , 'DELIVERY' ] ,
        ]);

        $this->assertSame( 'Gamme bien reçue, chiffrage attendu.' , $report->text    );
        $this->assertSame( 'QUOTE'                                , $report->outcome );
        $this->assertSame( 'GREEN'                                , $report->mood    );
        $this->assertSame( [ 'PRICING' , 'DELIVERY' ]             , $report->topics  );
    }

    /**
     * The author of a report is the person who wrote it — the property could not
     * hold one until its declared type said so.
     */
    public function testTheAuthorIsAPerson(): void
    {
        $report = new VisitReport([ Schema::AUTHOR => new Seller([ Schema::NAME => 'A. Perez' ] ) ] );

        $this->assertInstanceOf( Seller::class , $report->author       );
        $this->assertSame( 'A. Perez'          , $report->author->name );
    }

    /**
     * How it went and what it produced are two readings, and one does not stand in
     * for the other : a warm meeting can produce nothing at all.
     */
    public function testMoodAndOutcomeAreIndependent(): void
    {
        $report = new VisitReport([ Oihana::MOOD => 'GREEN' , Oihana::OUTCOME => 'NONE' ] );

        $this->assertSame( 'GREEN' , $report->mood    );
        $this->assertSame( 'NONE'  , $report->outcome );
    }

    /**
     * A report reduced to codes loses what makes it worth reading, and one reduced
     * to prose cannot be counted : the class declares both and requires neither.
     */
    public function testATextOnlyReportIsValid(): void
    {
        $report = new VisitReport([ Schema::TEXT => 'Rien à signaler.' ] );

        $this->assertSame( 'Rien à signaler.' , $report->text );
        $this->assertNull( $report->outcome ?? null );
        $this->assertNull( $report->mood    ?? null );
    }

    /**
     * The constructor assigns what it is given, as it is given ; the typing of a
     * nested value is the hydration's work, and that is where the attribute is read.
     *
     * @throws ReflectionException
     */
    public function testAttendeesAreHydratedAsCustomerEmployees(): void
    {
        $report = new Reflection()->hydrate
        (
            [
                Schema::ATTENDEE => [ [ Schema::NAME => 'Claire Martin' , Schema::JOB_TITLE => 'ACH' ] ] ,
            ],
            VisitReport::class
        );

        $this->assertIsArray( $report->attendee );
        $this->assertInstanceOf( CustomerEmployee::class , $report->attendee[ 0 ] );
        $this->assertSame( 'Claire Martin' , $report->attendee[ 0 ]->name );
    }

    public function testItSerializesWhatItWasGiven(): void
    {
        $json = new VisitReport([ Oihana::MOOD => 'RED' , Schema::TEXT => 'Litige livraison.' ] )->jsonSerialize() ;

        $this->assertSame( 'RED'               , $json[ Oihana::MOOD ] ?? null );
        $this->assertSame( 'Litige livraison.' , $json[ Schema::TEXT ] ?? null );
    }
}
