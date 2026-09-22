<?php

namespace tests\xyz\oihana\schema\statistics ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\statistics\ObservationSeries;
use xyz\oihana\schema\statistics\Statistics;
use xyz\oihana\schema\statistics\StatisticsSummary;

class StatisticsSummaryTest extends TestCase
{
    public function testIsAStatisticsRecord(): void
    {
        $this->assertInstanceOf( Statistics::class , new StatisticsSummary() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , StatisticsSummary::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'numberOfItems' , StatisticsSummary::NUMBER_OF_ITEMS );

        // The same idea schema.org names on a list, reused rather than renamed.
        $this->assertSame( Schema::NUMBER_OF_ITEMS , StatisticsSummary::NUMBER_OF_ITEMS );

        $this->assertSame( 'orderBacklog'      , StatisticsSummary::ORDER_BACKLOG      );
        $this->assertSame( 'uninvoicedRevenue' , StatisticsSummary::UNINVOICED_REVENUE );
    }

    public function testItCarriesTheTenMeasures(): void
    {
        $summary = new StatisticsSummary() ;

        foreach ( self::MEASURES as $measure )
        {
            $this->assertNull( $summary->{ $measure } ?? null , $measure );
        }
    }

    public function testTheHeadIsInherited(): void
    {
        $summary = new StatisticsSummary
        ([
            StatisticsSummary::YEAR               => 2024 ,
            StatisticsSummary::DIRECTION          => BusinessDocumentDirection::SALE ,
            StatisticsSummary::OBSERVATION_PERIOD => 'P1M' ,
        ]);

        $this->assertSame( 2024 , $summary->year );
        $this->assertSame( BusinessDocumentDirection::SALE , $summary->direction );
        $this->assertSame( 'P1M' , $summary->observationPeriod );
    }

    public function testItCountsTheRecordsItSummed(): void
    {
        $summary = new StatisticsSummary([ StatisticsSummary::NUMBER_OF_ITEMS => 128 ]);

        $this->assertSame( 128 , $summary->numberOfItems );
    }

    /**
     * 🚨 The whole contract of the class : a summary of a plain selection is about
     * no one, and `null` would claim the subject is unknown where the truth is
     * that the question does not apply.
     *
     * The test fails the day someone redeclares `about` — or `assignedCompany` —
     * with a default, which is exactly what it is there for.
     */
    public function testASummaryOfAPlainSelectionSerializesNoSubject(): void
    {
        $summary = new StatisticsSummary
        ([
            StatisticsSummary::YEAR            => 2024 ,
            StatisticsSummary::NUMBER_OF_ITEMS => 128 ,
            StatisticsSummary::REVENUE         => new ObservationSeries([ Oihana::VALUE => 4820.50 ]) ,
        ]);

        $document = json_decode( json_encode( $summary ) , true );

        $this->assertSame( 'StatisticsSummary' , $document[ '@type' ] );
        $this->assertSame( 128 , $document[ StatisticsSummary::NUMBER_OF_ITEMS ] );

        $this->assertArrayNotHasKey( StatisticsSummary::ABOUT            , $document );
        $this->assertArrayNotHasKey( StatisticsSummary::ASSIGNED_COMPANY , $document );

        // And what was never given stays out, measures included.
        $this->assertArrayNotHasKey( StatisticsSummary::GROSS_MARGIN       , $document );
        $this->assertArrayNotHasKey( StatisticsSummary::ORDER_BACKLOG      , $document );
        $this->assertArrayNotHasKey( StatisticsSummary::UNINVOICED_REVENUE , $document );
    }

    /**
     * 🚨 A summary of salespeople's records keeps their trade not invoiced yet.
     *
     * The constructor keeps only the properties a class declares and drops the
     * others without a word : the test fails the day the summary stops declaring
     * them, and a sum of such records starts losing them.
     */
    public function testASummaryKeepsTheTradeNotInvoicedYet(): void
    {
        $summary = new StatisticsSummary
        ([
            StatisticsSummary::NUMBER_OF_ITEMS    => 4 ,
            StatisticsSummary::UNINVOICED_REVENUE => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUES => self::UNINVOICED ]) ,
            StatisticsSummary::ORDER_BACKLOG      => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUES => self::BACKLOG    ]) ,
        ]);

        $document = json_decode( json_encode( $summary ) , true );

        $this->assertSame( self::UNINVOICED , $document[ StatisticsSummary::UNINVOICED_REVENUE ][ Oihana::VALUES ] );
        $this->assertSame( self::BACKLOG    , $document[ StatisticsSummary::ORDER_BACKLOG      ][ Oihana::VALUES ] );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionReadsTheTradeNotInvoicedYetAsSeries(): void
    {
        $summary = new Reflection()->hydrate
        (
            [
                StatisticsSummary::UNINVOICED_REVENUE => [ 'unitCode' => 'EUR' , 'values' => self::UNINVOICED ] ,
                StatisticsSummary::ORDER_BACKLOG      => [ 'unitCode' => 'EUR' , 'values' => self::BACKLOG    ] ,
            ],
            StatisticsSummary::class
        );

        $this->assertInstanceOf( ObservationSeries::class , $summary->uninvoicedRevenue );
        $this->assertInstanceOf( ObservationSeries::class , $summary->orderBacklog );
        $this->assertSame( self::BACKLOG , $summary->orderBacklog->values );
    }

    /**
     * 🔑 The second state of `about` : a selection grouped by a dimension is about
     * that dimension's value — a summary grouped by point of sale *is* the figures
     * of that point of sale.
     */
    public function testASummaryGroupedByADimensionTakesItAsItsSubject(): void
    {
        $summary = new StatisticsSummary([ StatisticsSummary::ABOUT => '1' ]);

        $this->assertSame( '1' , $summary->about );

        $resolved = new StatisticsSummary([ StatisticsSummary::ABOUT => new Warehouse([ Oihana::ID => '1' ]) ]);

        $this->assertInstanceOf( Warehouse::class , $resolved->about );
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

        $summary = new Reflection()->hydrate( $document , StatisticsSummary::class );

        foreach ( self::MEASURES as $measure )
        {
            $this->assertInstanceOf( ObservationSeries::class , $summary->{ $measure } , $measure );
            $this->assertSame( [ 4 , 6 ] , $summary->{ $measure }->values , $measure );
        }
    }

    /**
     * A measure is summed term by term, so a summary carries the same twelve
     * positions as the records it adds up — the January of the summary is the sum
     * of the Januaries.
     */
    public function testAMeasureKeepsItsSeriesAndItsTotal(): void
    {
        $months = [ 401.20 , 388.00 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ] ;

        $summary = new StatisticsSummary
        ([
            StatisticsSummary::REVENUE => new ObservationSeries
            ([
                Oihana::UNIT_CODE => 'EUR' ,
                Oihana::VALUE     => 789.20 ,
                Oihana::VALUES    => $months ,
            ]) ,
        ]);

        $this->assertSame( 'EUR'  , $summary->revenue->unitCode );
        $this->assertSame( 789.20 , $summary->revenue->value );
        $this->assertSame( $months , $summary->revenue->values );
        $this->assertCount( 12 , $summary->revenue->values );
    }

    /**
     * What is ordered and not yet delivered, summed over the selection.
     */
    private const array BACKLOG = [ 0 , 0 , 16800 , 7200 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ] ;

    /**
     * The ten measures every family of statistics carries.
     */
    private const array MEASURES =
    [
        StatisticsSummary::AVERAGE_COST    ,
        StatisticsSummary::AVERAGE_MARGIN  ,
        StatisticsSummary::COST_PRICE      ,
        StatisticsSummary::GROSS_MARGIN    ,
        StatisticsSummary::PURCHASE_COST   ,
        StatisticsSummary::PURCHASE_MARGIN ,
        StatisticsSummary::QUANTITY        ,
        StatisticsSummary::REVENUE         ,
        StatisticsSummary::VOLUME          ,
        StatisticsSummary::WEIGHT          ,
    ];

    /**
     * What was delivered and not invoiced yet, summed over the selection.
     */
    private const array UNINVOICED = [ 0 , 0 , 14000 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ] ;
}
