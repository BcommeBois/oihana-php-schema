<?php

namespace tests\xyz\oihana\schema\shipping ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\enumerations\DayOfWeek;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\shipping\DeliveryRouteAssignment as DeliveryRouteAssignmentTrait;
use xyz\oihana\schema\shipping\DeliveryRouteAssignment;
use xyz\oihana\schema\thesaurus\DeliveryRouteTerm;

class DeliveryRouteAssignmentTest extends TestCase
{
    public function testDefaults(): void
    {
        $assignment = new DeliveryRouteAssignment();

        $this->assertNull( $assignment->byDay       ?? null );
        $this->assertNull( $assignment->endTime     ?? null );
        $this->assertNull( $assignment->position    ?? null );
        $this->assertNull( $assignment->route       ?? null );
        $this->assertNull( $assignment->startTime   ?? null );
        $this->assertNull( $assignment->weekFrom    ?? null );
        $this->assertNull( $assignment->weekThrough ?? null );
    }

    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new DeliveryRouteAssignment() );
    }

    public function testUsesItsConstantsTrait(): void
    {
        $this->assertContains( DeliveryRouteAssignmentTrait::class , class_uses( DeliveryRouteAssignment::class ) );
    }

    public function testContext(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , DeliveryRouteAssignment::CONTEXT );
    }

    public function testPropertyNameConstants(): void
    {
        $this->assertSame( 'byDay'       , DeliveryRouteAssignment::BY_DAY );
        $this->assertSame( 'endTime'     , DeliveryRouteAssignment::END_TIME );
        $this->assertSame( 'position'    , DeliveryRouteAssignment::POSITION );
        $this->assertSame( 'route'       , DeliveryRouteAssignment::ROUTE );
        $this->assertSame( 'startTime'   , DeliveryRouteAssignment::START_TIME );
        $this->assertSame( 'weekFrom'    , DeliveryRouteAssignment::WEEK_FROM );
        $this->assertSame( 'weekThrough' , DeliveryRouteAssignment::WEEK_THROUGH );
    }

    /**
     * The seven keys reach the aggregator, and the four shared with other entities
     * keep their existing value. A divergence would be fatal at class load, since
     * every declaring trait meets in {@see Oihana} — nothing else in the suite
     * would catch it.
     */
    public function testConstantsAreComposedIntoTheAggregator(): void
    {
        $this->assertSame( DeliveryRouteAssignment::BY_DAY       , Oihana::BY_DAY );
        $this->assertSame( DeliveryRouteAssignment::END_TIME     , Oihana::END_TIME );
        $this->assertSame( DeliveryRouteAssignment::POSITION     , Oihana::POSITION );
        $this->assertSame( DeliveryRouteAssignment::ROUTE        , Oihana::ROUTE );
        $this->assertSame( DeliveryRouteAssignment::START_TIME   , Oihana::START_TIME );
        $this->assertSame( DeliveryRouteAssignment::WEEK_FROM    , Oihana::WEEK_FROM );
        $this->assertSame( DeliveryRouteAssignment::WEEK_THROUGH , Oihana::WEEK_THROUGH );
    }

    public function testConstructorCopiesScalarProperties(): void
    {
        $assignment = new DeliveryRouteAssignment
        ([
            DeliveryRouteAssignment::ROUTE        => '01D' ,
            DeliveryRouteAssignment::BY_DAY       => [ DayOfWeek::FRIDAY ] ,
            DeliveryRouteAssignment::POSITION     => 12 ,
            DeliveryRouteAssignment::START_TIME   => '08:00' ,
            DeliveryRouteAssignment::END_TIME     => '12:00' ,
            DeliveryRouteAssignment::WEEK_FROM    => 14 ,
            DeliveryRouteAssignment::WEEK_THROUGH => 39 ,
        ]);

        $this->assertSame( '01D'   , $assignment->route );
        $this->assertSame( [ DayOfWeek::FRIDAY ] , $assignment->byDay );
        $this->assertSame( 12      , $assignment->position );
        $this->assertSame( '08:00' , $assignment->startTime );
        $this->assertSame( '12:00' , $assignment->endTime );
        $this->assertSame( 14      , $assignment->weekFrom );
        $this->assertSame( 39      , $assignment->weekThrough );
    }

    /**
     * The two hour bounds are independent : an address open from eight with no
     * closing constraint states a start and no end.
     */
    public function testHourBoundsAreIndependent(): void
    {
        $assignment = new DeliveryRouteAssignment([ DeliveryRouteAssignment::START_TIME => '08:00' ]);

        $this->assertSame( '08:00' , $assignment->startTime );
        $this->assertNull( $assignment->endTime ?? null );
    }

    /**
     * A bare route reference is kept as read : nothing has been joined yet, and
     * inventing a term out of a code would claim a label nobody read.
     *
     * @throws ReflectionException
     */
    public function testReflectionKeepsRouteRawWhenScalar(): void
    {
        $assignment = ( new Reflection() )->hydrate
        (
            [ DeliveryRouteAssignment::ROUTE => '01D' ] ,
            DeliveryRouteAssignment::class
        );

        $this->assertSame( '01D' , $assignment->route );
    }

    /**
     * The identity of the assignment is the identity of nothing : `id` and `name`
     * are inherited and stay free, so a consumer that needs to key an assignment
     * has somewhere to do it without touching `route`.
     */
    public function testInheritsThingIdentity(): void
    {
        $assignment = new DeliveryRouteAssignment([ 'id' => 'A-42' , 'name' => 'Friday stop' ]);

        $this->assertSame( 'A-42'        , $assignment->id );
        $this->assertSame( 'Friday stop' , $assignment->name );
    }

    /**
     * A house route term is assignable to `route` : it reaches the union through
     * {@see \org\schema\DefinedTerm}.
     */
    public function testRouteAcceptsAResolvedTerm(): void
    {
        $term = new DeliveryRouteTerm([ 'id' => '01D' , 'name' => 'West coast, midweek' ]);

        $assignment = new DeliveryRouteAssignment();
        $assignment->route = $term ;

        $this->assertSame( $term , $assignment->route );
    }

    /**
     * The week bounds are ISO week numbers, not dates : they stay plain integers
     * through the hydration path.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesWeekBoundsAsIntegers(): void
    {
        $assignment = ( new Reflection() )->hydrate
        (
            [
                DeliveryRouteAssignment::WEEK_FROM    => 14 ,
                DeliveryRouteAssignment::WEEK_THROUGH => 39 ,
            ],
            DeliveryRouteAssignment::class
        );

        $this->assertIsInt( $assignment->weekFrom );
        $this->assertIsInt( $assignment->weekThrough );
        $this->assertSame( 14 , $assignment->weekFrom );
        $this->assertSame( 39 , $assignment->weekThrough );
    }
}
