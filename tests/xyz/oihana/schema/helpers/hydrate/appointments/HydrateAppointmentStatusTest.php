<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\constants\Schema;

use xyz\oihana\schema\enumerations\AppointmentCancelled;
use xyz\oihana\schema\enumerations\AppointmentDone;
use xyz\oihana\schema\enumerations\AppointmentNoShow;
use xyz\oihana\schema\enumerations\AppointmentStatus;

use function xyz\oihana\schema\helpers\hydrate\appointments\hydrateAppointmentStatus;

final class HydrateAppointmentStatusTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesAStatusStatedAsAnObject(): void
    {
        $status = hydrateAppointmentStatus
        ([
            Schema::AT_TYPE     => 'AppointmentNoShow' ,
            Schema::DESCRIPTION => 'Nobody at the office.' ,
        ]) ;

        $this->assertInstanceOf( AppointmentNoShow::class , $status ) ;
        $this->assertSame( 'Nobody at the office.' , $status->description ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAStatusFromItsStatedUri(): void
    {
        $status = hydrateAppointmentStatus( [ Schema::ADDITIONAL_TYPE => AppointmentStatus::DONE ] ) ;

        $this->assertInstanceOf( AppointmentDone::class , $status ) ;
    }

    /**
     * 🔑 The class name and the URI do not spell one another here — `AppointmentNoShow`
     * against `…/AppointmentStatus#NoShow` — so the member has to state its URI rather
     * than have it derived from its name.
     */
    public function testTheUriIsStatedRatherThanDerivedFromTheClassName(): void
    {
        $this->assertSame( AppointmentStatus::NO_SHOW , new AppointmentNoShow()->additionalType ) ;
        $this->assertNotSame( AppointmentStatus::NO_SHOW , AppointmentNoShow::getSchemaType() ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateAppointmentStatus() ) ;
        $this->assertSame( AppointmentStatus::DONE , hydrateAppointmentStatus( AppointmentStatus::DONE ) ) ;
        $this->assertSame( 'raw' , hydrateAppointmentStatus( 'raw' ) ) ;

        $done = new AppointmentDone() ;
        $this->assertSame( $done , hydrateAppointmentStatus( $done ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testAnUnknownStatusKeepsItsReason(): void
    {
        $status = hydrateAppointmentStatus
        ([
            Schema::AT_TYPE     => 'AppointmentForgotten' ,
            Schema::DESCRIPTION => 'Nobody wrote it down.' ,
        ]) ;

        $this->assertInstanceOf( AppointmentStatus::class , $status ) ;
        $this->assertSame( 'Nobody wrote it down.' , $status->description ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $statuses = hydrateAppointmentStatus
        ([
            [ Schema::AT_TYPE => 'AppointmentDone'      ] ,
            [ Schema::AT_TYPE => 'AppointmentCancelled' ] ,
        ]) ;

        $this->assertIsArray( $statuses ) ;
        $this->assertCount( 2 , $statuses ) ;
        $this->assertContainsOnlyInstancesOf( AppointmentStatus::class , $statuses ) ;

        $this->assertNull( hydrateAppointmentStatus( [] ) ) ;
    }

    /**
     * The promise the pair holds : the bare constant and the member class answer the
     * **same identifier**, before and after a round trip through `json_encode()` and
     * hydration — so « the cancelled meetings » is one filter, not two.
     *
     * @throws ReflectionException
     */
    public function testBothFormsAnswerTheSameIdentifierAcrossARoundTrip(): void
    {
        $bare   = AppointmentStatus::CANCELLED ;
        $stated = new AppointmentCancelled( [ Schema::DESCRIPTION => 'The customer postponed.' ] ) ;

        $this->assertSame( $bare , $stated->additionalType ) ;

        $read = hydrateAppointmentStatus( json_decode( json_encode( $stated ) , true ) ) ;

        $this->assertInstanceOf( AppointmentCancelled::class , $read ) ;
        $this->assertSame( $bare , $read->additionalType ) ;
        $this->assertSame( 'The customer postponed.' , $read->description ) ;

        $this->assertSame( $bare , hydrateAppointmentStatus( json_decode( json_encode( $bare ) , true ) ) ) ;
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
        $bare = hydrateAppointmentStatus( [ AppointmentStatus::PLANNED , AppointmentStatus::PLANNED ] ) ;

        $this->assertSame( [ AppointmentStatus::PLANNED , AppointmentStatus::PLANNED ] , $bare ) ;

        $mixed = hydrateAppointmentStatus( [ AppointmentStatus::PLANNED , [ Schema::AT_TYPE => 'AppointmentDone' ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( AppointmentStatus::PLANNED , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( AppointmentDone::class , $mixed[ 1 ] ) ;
    }
}
