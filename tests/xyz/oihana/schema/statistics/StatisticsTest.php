<?php

namespace tests\xyz\oihana\schema\statistics ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Intangible;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\statistics\Statistics;

class StatisticsTest extends TestCase
{
    public function testIsIntangible(): void
    {
        $this->assertInstanceOf( Intangible::class , new Statistics() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , Statistics::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'about'             , Statistics::ABOUT              );
        $this->assertSame( 'assignedCompany'   , Statistics::ASSIGNED_COMPANY   );
        $this->assertSame( 'direction'         , Statistics::DIRECTION          );
        $this->assertSame( 'observationPeriod' , Statistics::OBSERVATION_PERIOD );
        $this->assertSame( 'year'              , Statistics::YEAR               );

        $this->assertSame( Oihana::YEAR , Statistics::YEAR );
    }

    public function testDefaults(): void
    {
        $statistics = new Statistics() ;

        $this->assertNull( $statistics->about             ?? null );
        $this->assertNull( $statistics->assignedCompany   ?? null );
        $this->assertNull( $statistics->direction         ?? null );
        $this->assertNull( $statistics->observationPeriod ?? null );
        $this->assertNull( $statistics->year              ?? null );
    }

    public function testTheSubjectAndTheCompanyReadAsBareCodes(): void
    {
        $statistics = new Statistics
        ([
            Statistics::ABOUT            => '369980' ,
            Statistics::ASSIGNED_COMPANY => 501      ,
        ]);

        $this->assertSame( '369980' , $statistics->about );
        $this->assertSame( 501      , $statistics->assignedCompany );
    }

    public function testTheSubjectAndTheCompanyAlsoReadAsRows(): void
    {
        $statistics = new Statistics
        ([
            Statistics::ABOUT            => [ 'id' => '369980' , 'name' => 'Acme Joinery' ] ,
            Statistics::ASSIGNED_COMPANY => [ 'id' => '501'    , 'name' => 'Northern Timber' ] ,
        ]);

        $this->assertIsArray( $statistics->about );
        $this->assertIsArray( $statistics->assignedCompany );
    }

    public function testDirectionTakesTheEnumerationValue(): void
    {
        $statistics = new Statistics([ Statistics::DIRECTION => BusinessDocumentDirection::SALE ]);

        $this->assertSame( BusinessDocumentDirection::SALE , $statistics->direction );
    }

    public function testTheYearIsAnInteger(): void
    {
        $statistics = new Statistics([ Statistics::YEAR => 2025 ]);

        $this->assertSame( 2025 , $statistics->year );
    }

    public function testWhatIsNotSetIsNotSerialized(): void
    {
        $statistics = new Statistics([ Statistics::YEAR => 2025 ]);

        $document = json_decode( json_encode( $statistics ) , true );

        $this->assertSame( 2025 , $document[ Statistics::YEAR ] );
        $this->assertArrayNotHasKey( Statistics::ABOUT , $document );
        $this->assertArrayNotHasKey( Statistics::DIRECTION , $document );
    }

    public function testSerializesWithTheOihanaContext(): void
    {
        $document = json_decode( json_encode( new Statistics() ) , true );

        $this->assertSame( 'Statistics'  , $document[ '@type' ] );
        $this->assertSame( Oihana::SCHEMA , $document[ '@context' ] );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionReadsTheHead(): void
    {
        $statistics = new Reflection()->hydrate
        (
            [
                Statistics::ABOUT              => '369980' ,
                Statistics::DIRECTION          => BusinessDocumentDirection::SALE ,
                Statistics::YEAR               => 2025 ,
                Statistics::OBSERVATION_PERIOD => 'P1M' ,
                Statistics::ASSIGNED_COMPANY   => '501' ,
            ],
            Statistics::class
        );

        $this->assertInstanceOf( Statistics::class , $statistics );
        $this->assertSame( 2025  , $statistics->year );
        $this->assertSame( 'P1M' , $statistics->observationPeriod );
        $this->assertSame( '501' , $statistics->assignedCompany );
    }
}
