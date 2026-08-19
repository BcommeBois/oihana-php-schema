<?php

namespace tests\xyz\oihana\schema\statistics ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\people\Seller;
use xyz\oihana\schema\statistics\ObservationSeries;
use xyz\oihana\schema\statistics\SellerStatistics;
use xyz\oihana\schema\statistics\Statistics;

class SellerStatisticsTest extends TestCase
{
    public function testIsAStatisticsRecord(): void
    {
        $this->assertInstanceOf( Statistics::class , new SellerStatistics() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , SellerStatistics::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'assignedCustomer' , SellerStatistics::ASSIGNED_CUSTOMER );
    }

    public function testItCarriesTheTenMeasures(): void
    {
        $statistics = new SellerStatistics() ;

        foreach ( self::MEASURES as $measure )
        {
            $this->assertNull( $statistics->{ $measure } ?? null , $measure );
        }
    }

    public function testTheHeadIsInherited(): void
    {
        $statistics = new SellerStatistics
        ([
            SellerStatistics::YEAR      => 2025 ,
            SellerStatistics::DIRECTION => BusinessDocumentDirection::SALE ,
        ]);

        $this->assertSame( 2025 , $statistics->year );
        $this->assertSame( BusinessDocumentDirection::SALE , $statistics->direction );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionNamesTheSalespersonAndTheCustomer(): void
    {
        $statistics = new Reflection()->hydrate
        (
            [
                SellerStatistics::ABOUT             => [ 'id' => 'JDOE'   , 'name' => 'Jane Doe' ] ,
                SellerStatistics::ASSIGNED_CUSTOMER => [ 'id' => '369980' , 'name' => 'Acme Joinery' ] ,
            ],
            SellerStatistics::class
        );

        $this->assertInstanceOf( Seller::class   , $statistics->about );
        $this->assertInstanceOf( Customer::class , $statistics->assignedCustomer );
        $this->assertSame( 'JDOE' , $statistics->about->id );
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

        $statistics = new Reflection()->hydrate( $document , SellerStatistics::class );

        foreach ( self::MEASURES as $measure )
        {
            $this->assertInstanceOf( ObservationSeries::class , $statistics->{ $measure } , $measure );
            $this->assertSame( [ 4 , 6 ] , $statistics->{ $measure }->values , $measure );
        }
    }

    public function testASubjectAlsoReadsAsABareCode(): void
    {
        $statistics = new SellerStatistics([ SellerStatistics::ABOUT => 'JDOE' ]);

        $this->assertSame( 'JDOE' , $statistics->about );
    }

    /**
     * A source that totals the salesperson leaves the dimension unset, and the
     * record then says nothing about any customer at all.
     */
    public function testWhatIsNotSetIsNotSerialized(): void
    {
        $statistics = new SellerStatistics
        ([
            SellerStatistics::YEAR    => 2025 ,
            SellerStatistics::REVENUE => new ObservationSeries([ Oihana::VALUE => 100 ]) ,
        ]);

        $document = json_decode( json_encode( $statistics ) , true );

        $this->assertSame( 'SellerStatistics' , $document[ '@type' ] );
        $this->assertSame( 100 , $document[ SellerStatistics::REVENUE ][ Oihana::VALUE ] );
        $this->assertArrayNotHasKey( SellerStatistics::ASSIGNED_CUSTOMER , $document );
        $this->assertArrayNotHasKey( SellerStatistics::GROSS_MARGIN , $document );
    }

    /**
     * The ten measures every family of statistics carries.
     */
    private const array MEASURES =
    [
        SellerStatistics::AVERAGE_COST    ,
        SellerStatistics::AVERAGE_MARGIN  ,
        SellerStatistics::COST_PRICE      ,
        SellerStatistics::GROSS_MARGIN    ,
        SellerStatistics::PURCHASE_COST   ,
        SellerStatistics::PURCHASE_MARGIN ,
        SellerStatistics::QUANTITY        ,
        SellerStatistics::REVENUE         ,
        SellerStatistics::VOLUME          ,
        SellerStatistics::WEIGHT          ,
    ];
}
