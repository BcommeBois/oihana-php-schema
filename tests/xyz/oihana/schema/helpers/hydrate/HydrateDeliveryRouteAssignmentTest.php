<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\enumerations\DayOfWeek;

use xyz\oihana\schema\shipping\DeliveryRouteAssignment;
use xyz\oihana\schema\thesaurus\DeliveryRouteTerm;

use function xyz\oihana\schema\helpers\hydrate\hydrateDeliveryRouteAssignment;

class HydrateDeliveryRouteAssignmentTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testReturnsNonArrayValuesUntouched(): void
    {
        $this->assertNull( hydrateDeliveryRouteAssignment( null ) );
        $this->assertSame( '01D' , hydrateDeliveryRouteAssignment( '01D' ) );
        $this->assertSame( 42    , hydrateDeliveryRouteAssignment( 42 ) );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesASingleAssignment(): void
    {
        $assignment = hydrateDeliveryRouteAssignment
        ([
            DeliveryRouteAssignment::ROUTE      => '01D' ,
            DeliveryRouteAssignment::BY_DAY     => [ DayOfWeek::FRIDAY ] ,
            DeliveryRouteAssignment::POSITION   => 12 ,
            DeliveryRouteAssignment::START_TIME => '08:00' ,
        ]);

        $this->assertInstanceOf( DeliveryRouteAssignment::class , $assignment );
        $this->assertSame( '01D'   , $assignment->route );
        $this->assertSame( 12      , $assignment->position );
        $this->assertSame( '08:00' , $assignment->startTime );
    }

    /**
     * The list is the usual shape : a same address is commonly served by more
     * than one route.
     *
     * @throws ReflectionException
     */
    public function testHydratesAListOfAssignments(): void
    {
        $assignments = hydrateDeliveryRouteAssignment
        ([
            [ DeliveryRouteAssignment::ROUTE => '01D' , DeliveryRouteAssignment::BY_DAY => [ DayOfWeek::FRIDAY ] ] ,
            [ DeliveryRouteAssignment::ROUTE => '03'  , DeliveryRouteAssignment::BY_DAY => [ DayOfWeek::TUESDAY ] ] ,
        ]);

        $this->assertIsArray( $assignments );
        $this->assertCount( 2 , $assignments );
        $this->assertContainsOnlyInstancesOf( DeliveryRouteAssignment::class , $assignments );
        $this->assertSame( '01D' , $assignments[ 0 ]->route );
        $this->assertSame( '03'  , $assignments[ 1 ]->route );
    }

    /**
     * The joined reference row resolves into a term ; the label lives in the
     * thesaurus, so this is the only place it appears.
     *
     * @throws ReflectionException
     */
    public function testResolvesTheJoinedRouteIntoATerm(): void
    {
        $assignment = hydrateDeliveryRouteAssignment
        ([
            DeliveryRouteAssignment::ROUTE =>
            [
                'id'                            => '01D' ,
                'name'                          => 'West coast, midweek' ,
                DeliveryRouteTerm::ASSIGNED_POS => '1' ,
            ],
        ]);

        $this->assertInstanceOf( DeliveryRouteTerm::class , $assignment->route );
        $this->assertSame( '01D'                 , $assignment->route->id );
        $this->assertSame( 'West coast, midweek' , $assignment->route->name );
        $this->assertSame( '1'                   , $assignment->route->assignedPOS );
    }

    /**
     * A bare code is left alone : nothing has been joined, and building a term
     * out of a string would claim a label nobody read.
     *
     * @throws ReflectionException
     */
    public function testLeavesABareCodeAlone(): void
    {
        $assignment = hydrateDeliveryRouteAssignment([ DeliveryRouteAssignment::ROUTE => '01D' ]);

        $this->assertIsString( $assignment->route );
        $this->assertSame( '01D' , $assignment->route );
    }

    /**
     * An empty list yields null rather than an empty array — the shape the sibling
     * hydration helpers of the library already return.
     *
     * @throws ReflectionException
     */
    public function testEmptyListYieldsNull(): void
    {
        $this->assertNull( hydrateDeliveryRouteAssignment( [] ) );
    }
}
