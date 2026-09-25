<?php

namespace tests\xyz\oihana\schema\statistics ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\statistics\CustomerReceivables;
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

        $this->assertSame( 'observationDate' , StatisticsSummary::OBSERVATION_DATE );

        // The same date a receivables record is read on, and the Schema.org observation's :
        // declared twice with the same value, and PHP keeps them compatible as long as they agree.
        $this->assertSame( CustomerReceivables::OBSERVATION_DATE , StatisticsSummary::OBSERVATION_DATE );
        $this->assertSame( Oihana::OBSERVATION_DATE              , StatisticsSummary::OBSERVATION_DATE );

        $this->assertSame( 'orderBacklog'           , StatisticsSummary::ORDER_BACKLOG            );
        $this->assertSame( 'uninvoicedCostPrice'    , StatisticsSummary::UNINVOICED_COST_PRICE    );
        $this->assertSame( 'uninvoicedPurchaseCost' , StatisticsSummary::UNINVOICED_PURCHASE_COST );
        $this->assertSame( 'uninvoicedRevenue'      , StatisticsSummary::UNINVOICED_REVENUE       );
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
        $this->assertArrayNotHasKey( StatisticsSummary::GROSS_MARGIN          , $document );
        $this->assertArrayNotHasKey( StatisticsSummary::ORDER_BACKLOG         , $document );
        $this->assertArrayNotHasKey( StatisticsSummary::UNINVOICED_COST_PRICE    , $document );
        $this->assertArrayNotHasKey( StatisticsSummary::UNINVOICED_PURCHASE_COST , $document );
        $this->assertArrayNotHasKey( StatisticsSummary::UNINVOICED_REVENUE       , $document );
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
            StatisticsSummary::NUMBER_OF_ITEMS          => 4 ,
            StatisticsSummary::UNINVOICED_REVENUE       => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUES => self::UNINVOICED          ]) ,
            StatisticsSummary::UNINVOICED_COST_PRICE    => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUES => self::UNINVOICED_COST     ]) ,
            StatisticsSummary::UNINVOICED_PURCHASE_COST => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUES => self::UNINVOICED_PURCHASE ]) ,
            StatisticsSummary::ORDER_BACKLOG            => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUES => self::BACKLOG             ]) ,
        ]);

        $document = json_decode( json_encode( $summary ) , true );

        $this->assertSame( self::UNINVOICED          , $document[ StatisticsSummary::UNINVOICED_REVENUE       ][ Oihana::VALUES ] );
        $this->assertSame( self::UNINVOICED_COST     , $document[ StatisticsSummary::UNINVOICED_COST_PRICE    ][ Oihana::VALUES ] );
        $this->assertSame( self::UNINVOICED_PURCHASE , $document[ StatisticsSummary::UNINVOICED_PURCHASE_COST ][ Oihana::VALUES ] );
        $this->assertSame( self::BACKLOG             , $document[ StatisticsSummary::ORDER_BACKLOG            ][ Oihana::VALUES ] );
    }

    /**
     * ⛔ A summary does not carry a margin rate, and it must not start to.
     *
     * A summary adds up what it holds, term by term. A rate does not add up : the
     * hundred targets of one salesperson would answer a percentage in the
     * thousands — wrong, and plausible enough to be believed. The rate stays on
     * the target, where it means something.
     */
    public function testASummaryDoesNotCarryAMarginRate(): void
    {
        $this->assertFalse( property_exists( StatisticsSummary::class , Oihana::MARGIN_RATE ) );

        $summary = new StatisticsSummary
        ([
            StatisticsSummary::NUMBER_OF_ITEMS => 106 ,
            Oihana::MARGIN_RATE                => [ Oihana::UNIT_CODE => 'P1' , Oihana::VALUE => 25 ] ,
        ]);

        $document = json_decode( json_encode( $summary ) , true );

        $this->assertArrayNotHasKey( Oihana::MARGIN_RATE , $document );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionReadsTheTradeNotInvoicedYetAsSeries(): void
    {
        $summary = new Reflection()->hydrate
        (
            [
                StatisticsSummary::UNINVOICED_REVENUE       => [ 'unitCode' => 'EUR' , 'values' => self::UNINVOICED          ] ,
                StatisticsSummary::UNINVOICED_COST_PRICE    => [ 'unitCode' => 'EUR' , 'values' => self::UNINVOICED_COST     ] ,
                StatisticsSummary::UNINVOICED_PURCHASE_COST => [ 'unitCode' => 'EUR' , 'values' => self::UNINVOICED_PURCHASE ] ,
                StatisticsSummary::ORDER_BACKLOG            => [ 'unitCode' => 'EUR' , 'values' => self::BACKLOG             ] ,
            ],
            StatisticsSummary::class
        );

        $this->assertInstanceOf( ObservationSeries::class , $summary->uninvoicedRevenue );
        $this->assertInstanceOf( ObservationSeries::class , $summary->uninvoicedCostPrice );
        $this->assertInstanceOf( ObservationSeries::class , $summary->uninvoicedPurchaseCost );
        $this->assertInstanceOf( ObservationSeries::class , $summary->orderBacklog );
        $this->assertSame( self::UNINVOICED_COST     , $summary->uninvoicedCostPrice->values );
        $this->assertSame( self::UNINVOICED_PURCHASE , $summary->uninvoicedPurchaseCost->values );
        $this->assertSame( self::BACKLOG             , $summary->orderBacklog->values );
    }

    /**
     * 🚨 A summary of records of what is owed and late keeps their figures.
     *
     * Same reason as the trade not invoiced yet : the constructor keeps only the
     * properties a class declares, and a sum of such records built without them
     * would come out with its overdue gone.
     */
    public function testASummaryKeepsTheReceivables(): void
    {
        $summary = new StatisticsSummary
        ([
            StatisticsSummary::NUMBER_OF_ITEMS     => 5 ,
            StatisticsSummary::NUMBER_OF_DOCUMENTS => 12 ,
            StatisticsSummary::OVERDUE             => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUE => 21250 ]) ,
            StatisticsSummary::OVERDUE_OVER_90     => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUE =>  2650 ]) ,
            StatisticsSummary::DAYS_LATE           => new ObservationSeries([ Oihana::UNIT_CODE => 'DAY' , Oihana::VALUE =>    98 ]) ,
        ]);

        $document = json_decode( json_encode( $summary ) , true );

        $this->assertSame( 21250 , $document[ StatisticsSummary::OVERDUE         ][ Oihana::VALUE ] );
        $this->assertSame(  2650 , $document[ StatisticsSummary::OVERDUE_OVER_90 ][ Oihana::VALUE ] );
        $this->assertSame(    98 , $document[ StatisticsSummary::DAYS_LATE       ][ Oihana::VALUE ] );
        $this->assertSame(    12 , $document[ StatisticsSummary::NUMBER_OF_DOCUMENTS ] );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionReadsTheReceivablesAsSeries(): void
    {
        $summary = new Reflection()->hydrate
        (
            [
                StatisticsSummary::OBSERVATION_DATE => '2026-09-22' ,
                StatisticsSummary::OVERDUE          => [ 'unitCode' => 'EUR' , 'value' => 21250 ] ,
                StatisticsSummary::DAYS_LATE        => [ 'unitCode' => 'DAY' , 'value' =>    98 ] ,
            ],
            StatisticsSummary::class
        );

        $this->assertInstanceOf( ObservationSeries::class , $summary->overdue );
        $this->assertInstanceOf( ObservationSeries::class , $summary->daysLate );
        $this->assertSame( 21250 , $summary->overdue->value );
        $this->assertSame( 'DAY' , $summary->daysLate->unitCode );
        $this->assertNull( $summary->overdue->values ?? null );
        $this->assertSame( '2026-09-22' , $summary->observationDate );
    }

    /**
     * 🔑 A summary of records that are snapshots is a snapshot too : it says which
     * day it was read on, under the same name as the records, and carries no year.
     *
     * The test fails the day the summary stops declaring the date — the
     * constructor would then drop it without a word, and a sum of what is owed
     * would no longer say when it was true.
     */
    public function testASummaryOfSnapshotsSaysWhichDayItWasReadOn(): void
    {
        $summary = new StatisticsSummary
        ([
            StatisticsSummary::OBSERVATION_DATE    => '2026-09-22' ,
            StatisticsSummary::DIRECTION           => BusinessDocumentDirection::SALE ,
            StatisticsSummary::NUMBER_OF_ITEMS     => 5 ,
            StatisticsSummary::NUMBER_OF_DOCUMENTS => 12 ,
            StatisticsSummary::OVERDUE             => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUE => 21250 ]) ,
        ]);

        $this->assertSame( '2026-09-22' , $summary->observationDate );

        $document = json_decode( json_encode( $summary ) , true );

        $this->assertSame( '2026-09-22' , $document[ StatisticsSummary::OBSERVATION_DATE ] );
        $this->assertSame( 21250        , $document[ StatisticsSummary::OVERDUE ][ Oihana::VALUE ] );

        // A snapshot has no year and no step, and says neither.
        $this->assertArrayNotHasKey( StatisticsSummary::YEAR               , $document );
        $this->assertArrayNotHasKey( StatisticsSummary::OBSERVATION_PERIOD , $document );
    }

    /**
     * And a summary of a year of trade is dated by its year alone : the reading
     * date stays unset, and is not serialized.
     */
    public function testASummaryOfAYearCarriesNoReadingDate(): void
    {
        $summary = new StatisticsSummary
        ([
            StatisticsSummary::YEAR            => 2024 ,
            StatisticsSummary::NUMBER_OF_ITEMS => 128 ,
        ]);

        $this->assertNull( $summary->observationDate ?? null );

        $document = json_decode( json_encode( $summary ) , true );

        $this->assertSame( 2024 , $document[ StatisticsSummary::YEAR ] );
        $this->assertArrayNotHasKey( StatisticsSummary::OBSERVATION_DATE , $document );
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

    /**
     * The cost price of what was delivered and not invoiced yet, summed over the selection.
     */
    private const array UNINVOICED_COST = [ 0 , 0 , 10900 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ] ;

    /**
     * The purchase cost of what was delivered and not invoiced yet, summed over the selection.
     */
    private const array UNINVOICED_PURCHASE = [ 0 , 0 , 10500 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ] ;
}
