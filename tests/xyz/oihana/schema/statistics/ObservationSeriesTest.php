<?php

namespace tests\xyz\oihana\schema\statistics ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Observation;
use org\schema\QuantitativeValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\statistics\ObservationSeries;

class ObservationSeriesTest extends TestCase
{
    public function testIsAnObservation(): void
    {
        $series = new ObservationSeries() ;

        $this->assertInstanceOf( Observation::class , $series );
        $this->assertInstanceOf( QuantitativeValue::class , $series );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , ObservationSeries::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'values' , ObservationSeries::VALUES );
        $this->assertSame( Oihana::VALUES , ObservationSeries::VALUES );
    }

    public function testDefaults(): void
    {
        $series = new ObservationSeries() ;

        $this->assertNull( $series->values    ?? null );
        $this->assertNull( $series->value     ?? null );
        $this->assertNull( $series->unitCode  ?? null );
    }

    public function testATotalAndItsRunTravelTogether(): void
    {
        $series = new ObservationSeries
        ([
            Oihana::UNIT_CODE  => 'EUR' ,
            Oihana::VALUE     => 60 ,
            ObservationSeries::VALUES    => [ 10 , 20 , 30 ] ,
        ]);

        $this->assertSame( 'EUR' , $series->unitCode );
        $this->assertSame( 60    , $series->value );
        $this->assertSame( [ 10 , 20 , 30 ] , $series->values );
    }

    public function testARunWithNoTotalSerializesWithoutOne(): void
    {
        $series = new ObservationSeries([ ObservationSeries::VALUES => [ 10 , 20 , 30 ] ]);

        $document = json_decode( json_encode( $series ) , true );

        $this->assertSame( [ 10 , 20 , 30 ] , $document[ ObservationSeries::VALUES ] );
        $this->assertArrayNotHasKey( Oihana::VALUE , $document );
    }

    public function testATotalWithNoRunSerializesWithoutOne(): void
    {
        $series = new ObservationSeries([ Oihana::VALUE => 66631.13 ]);

        $document = json_decode( json_encode( $series ) , true );

        $this->assertSame( 66631.13 , $document[ Oihana::VALUE ] );
        $this->assertArrayNotHasKey( ObservationSeries::VALUES , $document );
    }

    public function testSerializesWithTheOihanaContext(): void
    {
        $document = json_decode( json_encode( new ObservationSeries() ) , true );

        $this->assertSame( 'ObservationSeries' , $document[ '@type' ] );
        $this->assertSame( Oihana::SCHEMA , $document[ '@context' ] );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionReadsTheSeries(): void
    {
        $series = new Reflection()->hydrate
        (
            [
                Oihana::UNIT_CODE  => 'KGM' ,
                ObservationSeries::VALUES    => [ 1.5 , 2.5 ] ,
            ],
            ObservationSeries::class
        );

        $this->assertInstanceOf( ObservationSeries::class , $series );
        $this->assertSame( 'KGM' , $series->unitCode );
        $this->assertSame( [ 1.5 , 2.5 ] , $series->values );
    }
}
