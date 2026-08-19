<?php

namespace tests\xyz\oihana\schema\statistics ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Person;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\statistics\CustomerStatistics;
use xyz\oihana\schema\statistics\ObservationSeries;
use xyz\oihana\schema\statistics\Statistics;

class CustomerStatisticsTest extends TestCase
{
    public function testIsAStatisticsRecord(): void
    {
        $this->assertInstanceOf( Statistics::class , new CustomerStatistics() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , CustomerStatistics::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'assignedPOS'    , CustomerStatistics::ASSIGNED_POS    );
        $this->assertSame( 'assignedSeller' , CustomerStatistics::ASSIGNED_SELLER );

        $this->assertSame( Oihana::ASSIGNED_POS , CustomerStatistics::ASSIGNED_POS );
    }

    public function testItCarriesTheTenMeasures(): void
    {
        $statistics = new CustomerStatistics() ;

        foreach ( self::MEASURES as $measure )
        {
            $this->assertNull( $statistics->{ $measure } ?? null , $measure );
        }
    }

    public function testTheHeadIsInherited(): void
    {
        $statistics = new CustomerStatistics
        ([
            CustomerStatistics::YEAR      => 2025 ,
            CustomerStatistics::DIRECTION => BusinessDocumentDirection::SALE ,
        ]);

        $this->assertSame( 2025 , $statistics->year );
        $this->assertSame( BusinessDocumentDirection::SALE , $statistics->direction );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionNamesTheSubjectAndTheDimensions(): void
    {
        $statistics = new Reflection()->hydrate
        (
            [
                CustomerStatistics::ABOUT           => [ 'id' => '369980' , 'name' => 'Acme Joinery' ] ,
                CustomerStatistics::ASSIGNED_SELLER => [ 'id' => 'JDOE'   , 'name' => 'Jane Doe' ] ,
                CustomerStatistics::ASSIGNED_POS    => [ 'id' => '1'      , 'name' => 'North yard' ] ,
            ],
            CustomerStatistics::class
        );

        $this->assertInstanceOf( Customer::class  , $statistics->about );
        $this->assertInstanceOf( Person::class    , $statistics->assignedSeller );
        $this->assertInstanceOf( Warehouse::class , $statistics->assignedPOS );
        $this->assertSame( '369980' , $statistics->about->id );
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

        $statistics = new Reflection()->hydrate( $document , CustomerStatistics::class );

        foreach ( self::MEASURES as $measure )
        {
            $this->assertInstanceOf( ObservationSeries::class , $statistics->{ $measure } , $measure );
            $this->assertSame( [ 4 , 6 ] , $statistics->{ $measure }->values , $measure );
        }
    }

    public function testASubjectAlsoReadsAsABareCode(): void
    {
        $statistics = new CustomerStatistics([ CustomerStatistics::ABOUT => '369980' ]);

        $this->assertSame( '369980' , $statistics->about );
    }

    public function testWhatIsNotSetIsNotSerialized(): void
    {
        $statistics = new CustomerStatistics
        ([
            CustomerStatistics::YEAR    => 2025 ,
            CustomerStatistics::REVENUE => new ObservationSeries([ Oihana::VALUE => 100 ]) ,
        ]);

        $document = json_decode( json_encode( $statistics ) , true );

        $this->assertSame( 'CustomerStatistics' , $document[ '@type' ] );
        $this->assertSame( 100 , $document[ CustomerStatistics::REVENUE ][ Oihana::VALUE ] );
        $this->assertArrayNotHasKey( CustomerStatistics::GROSS_MARGIN , $document );
        $this->assertArrayNotHasKey( CustomerStatistics::ASSIGNED_SELLER , $document );
    }

    /**
     * The ten measures every family of statistics carries.
     */
    private const array MEASURES =
    [
        CustomerStatistics::AVERAGE_COST    ,
        CustomerStatistics::AVERAGE_MARGIN  ,
        CustomerStatistics::COST_PRICE      ,
        CustomerStatistics::GROSS_MARGIN    ,
        CustomerStatistics::PURCHASE_COST   ,
        CustomerStatistics::PURCHASE_MARGIN ,
        CustomerStatistics::QUANTITY        ,
        CustomerStatistics::REVENUE         ,
        CustomerStatistics::VOLUME          ,
        CustomerStatistics::WEIGHT          ,
    ];
}
