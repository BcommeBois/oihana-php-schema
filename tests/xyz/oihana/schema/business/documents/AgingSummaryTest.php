<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\AgingSummary;
use xyz\oihana\schema\constants\Oihana;

class AgingSummaryTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new AgingSummary() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , AgingSummary::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'current'    , AgingSummary::CURRENT       );
        $this->assertSame( 'days1To30'  , AgingSummary::DAYS_1_TO_30  );
        $this->assertSame( 'days31To60' , AgingSummary::DAYS_31_TO_60 );
        $this->assertSame( 'days61To90' , AgingSummary::DAYS_61_TO_90 );
        $this->assertSame( 'over90'     , AgingSummary::OVER_90       );

        $this->assertSame( Oihana::CURRENT , AgingSummary::CURRENT );
    }

    public function testDefaults(): void
    {
        $aging = new AgingSummary() ;

        $this->assertNull( $aging->current    ?? null );
        $this->assertNull( $aging->days1To30  ?? null );
        $this->assertNull( $aging->days31To60 ?? null );
        $this->assertNull( $aging->days61To90 ?? null );
        $this->assertNull( $aging->over90     ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesBuckets(): void
    {
        $aging = new Reflection()->hydrate
        (
            [
                AgingSummary::CURRENT => [ 'value' => 100 , 'currency' => 'EUR' ] ,
                AgingSummary::OVER_90 => [ 'value' => 20  , 'currency' => 'EUR' ] ,
            ],
            AgingSummary::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $aging->current ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $aging->over90 ) ;
        $this->assertSame( 100 , $aging->current->value ) ;
    }
}
