<?php

namespace tests\xyz\oihana\schema\statistics ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\QuantitativeValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\people\Seller;
use xyz\oihana\schema\statistics\ObservationSeries;
use xyz\oihana\schema\statistics\SalesObjectives;
use xyz\oihana\schema\statistics\Statistics;

class SalesObjectivesTest extends TestCase
{
    public function testIsAStatisticsRecord(): void
    {
        $this->assertInstanceOf( Statistics::class , new SalesObjectives() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , SalesObjectives::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'assignedCategory' , SalesObjectives::ASSIGNED_CATEGORY );
        $this->assertSame( 'assignedCustomer' , SalesObjectives::ASSIGNED_CUSTOMER );
        $this->assertSame( 'marginRate'       , SalesObjectives::MARGIN_RATE       );
    }

    public function testItCarriesTheTenMeasures(): void
    {
        $objectives = new SalesObjectives() ;

        foreach ( self::MEASURES as $measure )
        {
            $this->assertNull( $objectives->{ $measure } ?? null , $measure );
        }
    }

    public function testTheHeadIsInherited(): void
    {
        $objectives = new SalesObjectives
        ([
            SalesObjectives::YEAR      => 2026 ,
            SalesObjectives::DIRECTION => BusinessDocumentDirection::SALE ,
        ]);

        $this->assertSame( 2026 , $objectives->year );
        $this->assertSame( BusinessDocumentDirection::SALE , $objectives->direction );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionNamesTheSalespersonAndTheCustomer(): void
    {
        $objectives = new Reflection()->hydrate
        (
            [
                SalesObjectives::ABOUT             => [ 'id' => 'JDOE'   , 'name' => 'Jane Doe' ] ,
                SalesObjectives::ASSIGNED_CUSTOMER => [ 'id' => '369980' , 'name' => 'Acme Joinery' ] ,
            ],
            SalesObjectives::class
        );

        $this->assertInstanceOf( Seller::class   , $objectives->about );
        $this->assertInstanceOf( Customer::class , $objectives->assignedCustomer );
        $this->assertSame( 'JDOE' , $objectives->about->id );
    }

    /**
     * A path through a classification is read back as it was written : the
     * ordered codes of its levels, widest first, left alone.
     *
     * @throws ReflectionException
     */
    public function testACategoryReadsAsThePathOfItsLevels(): void
    {
        $objectives = new Reflection()->hydrate
        (
            [ SalesObjectives::ASSIGNED_CATEGORY => [ '5' , '1' ] ] ,
            SalesObjectives::class
        );

        $this->assertSame( [ '5' , '1' ] , $objectives->assignedCategory );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionReadsEveryMeasureAsASeries(): void
    {
        $document = [] ;

        foreach ( self::MEASURES as $measure )
        {
            $document[ $measure ] = [ 'unitCode' => 'EUR' , 'value' => 10 , 'values' => [ 4 , 6 ] ] ;
        }

        $objectives = new Reflection()->hydrate( $document , SalesObjectives::class );

        foreach ( self::MEASURES as $measure )
        {
            $this->assertInstanceOf( ObservationSeries::class , $objectives->{ $measure } , $measure );
            $this->assertSame( [ 4 , 6 ] , $objectives->{ $measure }->values , $measure );
        }
    }

    public function testASubjectAlsoReadsAsABareCode(): void
    {
        $objectives = new SalesObjectives([ SalesObjectives::ABOUT => 'JDOE' ]);

        $this->assertSame( 'JDOE' , $objectives->about );
    }

    /**
     * The two narrowings are alternatives : a target set on a customer says
     * nothing about any range of goods, and a source that publishes one measure
     * publishes one.
     */
    public function testWhatIsNotSetIsNotSerialized(): void
    {
        $objectives = new SalesObjectives
        ([
            SalesObjectives::YEAR              => 2026 ,
            SalesObjectives::ASSIGNED_CUSTOMER => '369980' ,
            SalesObjectives::REVENUE           => new ObservationSeries([ Oihana::VALUE => 3000 ]) ,
        ]);

        $document = json_decode( json_encode( $objectives ) , true );

        $this->assertSame( 'SalesObjectives' , $document[ '@type' ] );
        $this->assertSame( 3000 , $document[ SalesObjectives::REVENUE ][ Oihana::VALUE ] );
        $this->assertSame( '369980' , $document[ SalesObjectives::ASSIGNED_CUSTOMER ] );
        $this->assertArrayNotHasKey( SalesObjectives::ASSIGNED_CATEGORY , $document );
        $this->assertArrayNotHasKey( SalesObjectives::GROSS_MARGIN , $document );
        $this->assertArrayNotHasKey( SalesObjectives::MARGIN_RATE , $document );
    }

    /**
     * A rate is one number and its unit, never a run of twelve : the percent code
     * says once and for all that `25` means 25 %, and not 0.25 nor 2 500 %.
     *
     * @throws ReflectionException
     */
    public function testTheMarginRateReadsAsAPercentage(): void
    {
        $objectives = new Reflection()->hydrate
        (
            [ SalesObjectives::MARGIN_RATE => [ Oihana::UNIT_CODE => 'P1' , Oihana::VALUE => 25 ] ] ,
            SalesObjectives::class
        );

        $this->assertInstanceOf( QuantitativeValue::class , $objectives->marginRate );
        $this->assertEquals( 25 , $objectives->marginRate->value );
        $this->assertSame( 'P1' , $objectives->marginRate->unitCode );
    }

    /**
     * A rate is not one of the measures : it carries no run of twelve values, and
     * nothing sums it.
     *
     * @throws ReflectionException
     */
    public function testTheMarginRateCarriesNoRun(): void
    {
        $objectives = new Reflection()->hydrate
        (
            [ SalesObjectives::MARGIN_RATE => [ Oihana::UNIT_CODE => 'P1' , Oihana::VALUE => 23.5 ] ] ,
            SalesObjectives::class
        );

        $document = json_decode( json_encode( $objectives ) , true );

        $this->assertEquals( 23.5 , $document[ SalesObjectives::MARGIN_RATE ][ Oihana::VALUE ] );
        $this->assertArrayNotHasKey( Oihana::VALUES , $document[ SalesObjectives::MARGIN_RATE ] );
    }

    /**
     * The ten measures every family of statistics carries.
     */
    private const array MEASURES =
    [
        SalesObjectives::AVERAGE_COST    ,
        SalesObjectives::AVERAGE_MARGIN  ,
        SalesObjectives::COST_PRICE      ,
        SalesObjectives::GROSS_MARGIN    ,
        SalesObjectives::PURCHASE_COST   ,
        SalesObjectives::PURCHASE_MARGIN ,
        SalesObjectives::QUANTITY        ,
        SalesObjectives::REVENUE         ,
        SalesObjectives::VOLUME          ,
        SalesObjectives::WEIGHT          ,
    ];
}
