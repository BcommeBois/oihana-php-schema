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
        $this->assertSame( 'assignedCustomer'    , SellerStatistics::ASSIGNED_CUSTOMER     );
        $this->assertSame( 'orderBacklog'        , SellerStatistics::ORDER_BACKLOG         );
        $this->assertSame( 'uninvoicedCostPrice' , SellerStatistics::UNINVOICED_COST_PRICE );
        $this->assertSame( 'uninvoicedRevenue'   , SellerStatistics::UNINVOICED_REVENUE    );

        // The aggregator composes the new constants trait — a name clash there would be fatal.
        $this->assertSame( Oihana::ORDER_BACKLOG         , SellerStatistics::ORDER_BACKLOG         );
        $this->assertSame( Oihana::UNINVOICED_COST_PRICE , SellerStatistics::UNINVOICED_COST_PRICE );
        $this->assertSame( Oihana::UNINVOICED_REVENUE    , SellerStatistics::UNINVOICED_REVENUE    );
    }

    public function testItCarriesTheTenMeasures(): void
    {
        $statistics = new SellerStatistics() ;

        foreach ( self::MEASURES as $measure )
        {
            $this->assertNull( $statistics->{ $measure } ?? null , $measure );
        }
    }

    public function testItCarriesTheTradeNotInvoicedYet(): void
    {
        $statistics = new SellerStatistics() ;

        $this->assertNull( $statistics->orderBacklog        ?? null );
        $this->assertNull( $statistics->uninvoicedCostPrice ?? null );
        $this->assertNull( $statistics->uninvoicedRevenue   ?? null );
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

    /**
     * The trade not invoiced yet is a run only : a stored row carries `values`,
     * and no total.
     *
     * @throws ReflectionException
     */
    public function testReflectionReadsTheTradeNotInvoicedYetAsSeries(): void
    {
        $statistics = new Reflection()->hydrate
        (
            [
                SellerStatistics::UNINVOICED_REVENUE    => [ 'unitCode' => 'EUR' , 'values' => self::UNINVOICED      ] ,
                SellerStatistics::UNINVOICED_COST_PRICE => [ 'unitCode' => 'EUR' , 'values' => self::UNINVOICED_COST ] ,
                SellerStatistics::ORDER_BACKLOG         => [ 'unitCode' => 'EUR' , 'values' => self::BACKLOG         ] ,
            ],
            SellerStatistics::class
        );

        $this->assertInstanceOf( ObservationSeries::class , $statistics->uninvoicedRevenue );
        $this->assertInstanceOf( ObservationSeries::class , $statistics->uninvoicedCostPrice );
        $this->assertInstanceOf( ObservationSeries::class , $statistics->orderBacklog );
        $this->assertSame( self::UNINVOICED      , $statistics->uninvoicedRevenue->values );
        $this->assertSame( self::UNINVOICED_COST , $statistics->uninvoicedCostPrice->values );
        $this->assertSame( self::BACKLOG         , $statistics->orderBacklog->values );
        $this->assertNull( $statistics->uninvoicedRevenue->value ?? null );
        $this->assertNull( $statistics->uninvoicedCostPrice->value ?? null );
    }

    /**
     * 🔑 The three stages never overlap : what was invoiced in March plus what
     * was delivered in March and not invoiced yet is what was delivered in March.
     */
    public function testTheInvoicedAndTheUninvoicedAddUpToTheDelivered(): void
    {
        $statistics = new SellerStatistics
        ([
            SellerStatistics::REVENUE            => new ObservationSeries([ Oihana::VALUES => self::INVOICED   ]) ,
            SellerStatistics::UNINVOICED_REVENUE => new ObservationSeries([ Oihana::VALUES => self::UNINVOICED ]) ,
            SellerStatistics::ORDER_BACKLOG      => new ObservationSeries([ Oihana::VALUES => self::BACKLOG    ]) ,
        ]);

        $march = 2 ;

        $delivered = $statistics->revenue->values[ $march ] + $statistics->uninvoicedRevenue->values[ $march ] ;

        $this->assertSame( 15500 , $delivered );
        $this->assertSame( 19700 , $delivered + $statistics->orderBacklog->values[ $march ] );
    }

    /**
     * 💶 The cost of what was delivered adds up the same way, and the margin over
     * the delivered reads from the four runs — no margin series needed.
     */
    public function testTheMarginOverTheDeliveredReadsFromFourRuns(): void
    {
        $statistics = new SellerStatistics
        ([
            SellerStatistics::REVENUE               => new ObservationSeries([ Oihana::VALUES => self::INVOICED        ]) ,
            SellerStatistics::COST_PRICE            => new ObservationSeries([ Oihana::VALUES => self::INVOICED_COST   ]) ,
            SellerStatistics::UNINVOICED_REVENUE    => new ObservationSeries([ Oihana::VALUES => self::UNINVOICED      ]) ,
            SellerStatistics::UNINVOICED_COST_PRICE => new ObservationSeries([ Oihana::VALUES => self::UNINVOICED_COST ]) ,
        ]);

        $march = 2 ;

        $delivered = $statistics->revenue->values[ $march ]   + $statistics->uninvoicedRevenue->values[ $march ]   ;
        $cost      = $statistics->costPrice->values[ $march ] + $statistics->uninvoicedCostPrice->values[ $march ] ;

        $this->assertSame( 12000 , $cost );
        $this->assertSame( 3500  , $delivered - $cost );
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

        // A record whose trade is all invoiced carries no such series — absent, not twelve zeros.
        $this->assertArrayNotHasKey( SellerStatistics::ORDER_BACKLOG         , $document );
        $this->assertArrayNotHasKey( SellerStatistics::UNINVOICED_COST_PRICE , $document );
        $this->assertArrayNotHasKey( SellerStatistics::UNINVOICED_REVENUE    , $document );
    }

    /**
     * What is ordered and not yet delivered, month of the planned delivery — March,
     * and an April still to come.
     */
    private const array BACKLOG = [ 0 , 0 , 4200 , 1800 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ] ;

    /**
     * What was invoiced, month by month.
     */
    private const array INVOICED = [ 9800 , 11300 , 12000 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ] ;

    /**
     * The cost price of what was invoiced, month by month.
     */
    private const array INVOICED_COST = [ 7600 , 8700 , 9300 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ] ;

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

    /**
     * What was delivered and not invoiced yet — March only, the months before are invoiced.
     */
    private const array UNINVOICED = [ 0 , 0 , 3500 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ] ;

    /**
     * The cost price of what was delivered and not invoiced yet — March only.
     */
    private const array UNINVOICED_COST = [ 0 , 0 , 2700 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ] ;
}
