<?php

namespace tests\xyz\oihana\schema\statistics ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\DefinedTerm;
use org\schema\Person;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\statistics\CustomerReceivables;
use xyz\oihana\schema\statistics\ObservationSeries;
use xyz\oihana\schema\statistics\Statistics;

class CustomerReceivablesTest extends TestCase
{
    public function testIsAStatisticsRecord(): void
    {
        $this->assertInstanceOf( Statistics::class , new CustomerReceivables() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , CustomerReceivables::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'assignedPOS'       , CustomerReceivables::ASSIGNED_POS        );
        $this->assertSame( 'assignedSeller'    , CustomerReceivables::ASSIGNED_SELLER     );
        $this->assertSame( 'category'          , CustomerReceivables::CATEGORY            );
        $this->assertSame( 'daysLate'          , CustomerReceivables::DAYS_LATE           );
        $this->assertSame( 'doubtful'          , CustomerReceivables::DOUBTFUL            );
        $this->assertSame( 'numberOfDocuments' , CustomerReceivables::NUMBER_OF_DOCUMENTS );
        $this->assertSame( 'observationDate'   , CustomerReceivables::OBSERVATION_DATE    );
        $this->assertSame( 'overdue'           , CustomerReceivables::OVERDUE             );
        $this->assertSame( 'overdue1To30'      , CustomerReceivables::OVERDUE_1_TO_30     );
        $this->assertSame( 'overdue31To60'     , CustomerReceivables::OVERDUE_31_TO_60    );
        $this->assertSame( 'overdue61To90'     , CustomerReceivables::OVERDUE_61_TO_90    );
        $this->assertSame( 'overdueOver90'     , CustomerReceivables::OVERDUE_OVER_90     );

        // The aggregator composes the new constants traits. Two of the names
        // already exist in other traits it composes : PHP accepts the duplicate
        // as long as the values agree, and a drift would be fatal at loading.
        $this->assertSame( Oihana::CATEGORY         , CustomerReceivables::CATEGORY         );
        $this->assertSame( Oihana::OBSERVATION_DATE , CustomerReceivables::OBSERVATION_DATE );
        $this->assertSame( Oihana::OVERDUE          , CustomerReceivables::OVERDUE          );
        $this->assertSame( Oihana::DAYS_LATE        , CustomerReceivables::DAYS_LATE        );
    }

    public function testItCarriesTheReceivables(): void
    {
        $receivables = new CustomerReceivables() ;

        foreach ( self::MEASURES as $measure )
        {
            $this->assertNull( $receivables->{ $measure } ?? null , $measure );
        }

        $this->assertNull( $receivables->numberOfDocuments ?? null );
    }

    /**
     * A record of what is late carries no year of trade : no revenue, no margin.
     */
    public function testItDoesNotCarryTheTradingMeasures(): void
    {
        $this->assertFalse( property_exists( CustomerReceivables::class , Oihana::REVENUE      ) );
        $this->assertFalse( property_exists( CustomerReceivables::class , Oihana::GROSS_MARGIN ) );
        $this->assertFalse( property_exists( CustomerReceivables::class , Oihana::QUANTITY     ) );
    }

    public function testTheHeadIsInherited(): void
    {
        $receivables = new CustomerReceivables
        ([
            CustomerReceivables::ASSIGNED_COMPANY => '501' ,
            CustomerReceivables::DIRECTION        => BusinessDocumentDirection::SALE ,
            CustomerReceivables::OBSERVATION_DATE => '2026-09-22' ,
        ]);

        $this->assertSame( '501'        , $receivables->assignedCompany );
        $this->assertSame( '2026-09-22' , $receivables->observationDate );
        $this->assertSame( BusinessDocumentDirection::SALE , $receivables->direction );
        $this->assertNull( $receivables->year              ?? null );
        $this->assertNull( $receivables->observationPeriod ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionNamesTheSubjectAndTheDimensions(): void
    {
        $receivables = new Reflection()->hydrate
        (
            [
                CustomerReceivables::ABOUT           => [ 'id' => '369980' , 'name' => 'Vialaret Joinery' ] ,
                CustomerReceivables::ASSIGNED_SELLER => [ 'id' => 'JDOE'   , 'name' => 'Jane Doe' ] ,
                CustomerReceivables::ASSIGNED_POS    => [ 'id' => '1'      , 'name' => 'North yard' ] ,
                CustomerReceivables::CATEGORY        => [ 'id' => 'PRO'    , 'name' => 'Professional' ] ,
            ],
            CustomerReceivables::class
        );

        $this->assertInstanceOf( Customer::class    , $receivables->about );
        $this->assertInstanceOf( Person::class      , $receivables->assignedSeller );
        $this->assertInstanceOf( Warehouse::class   , $receivables->assignedPOS );
        $this->assertInstanceOf( DefinedTerm::class , $receivables->category );
        $this->assertSame( '369980' , $receivables->about->id );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionReadsEveryMeasureAsASeries(): void
    {
        $document = [ CustomerReceivables::NUMBER_OF_DOCUMENTS => 3 ] ;

        foreach ( self::MEASURES as $measure )
        {
            $document[ $measure ] = [ 'unitCode' => 'EUR' , 'value' => 10 ] ;
        }

        $receivables = new Reflection()->hydrate( $document , CustomerReceivables::class );

        foreach ( self::MEASURES as $measure )
        {
            $this->assertInstanceOf( ObservationSeries::class , $receivables->{ $measure } , $measure );
            $this->assertSame( 10    , $receivables->{ $measure }->value    , $measure );
            $this->assertSame( 'EUR' , $receivables->{ $measure }->unitCode , $measure );
            $this->assertNull( $receivables->{ $measure }->values ?? null , $measure );
        }

        $this->assertSame( 3 , $receivables->numberOfDocuments );
    }

    /**
     * 🔑 The four buckets add up to the overdue amount, the doubtful part never
     * exceeds it, and the count is a plain integer.
     */
    public function testTheBucketsAddUpToTheOverdue(): void
    {
        $receivables = new CustomerReceivables
        ([
            CustomerReceivables::OVERDUE             => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUE => 11150 ]) ,
            CustomerReceivables::OVERDUE_1_TO_30     => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUE =>  3100 ]) ,
            CustomerReceivables::OVERDUE_31_TO_60    => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUE =>     0 ]) ,
            CustomerReceivables::OVERDUE_61_TO_90    => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUE =>  5400 ]) ,
            CustomerReceivables::OVERDUE_OVER_90     => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUE =>  2650 ]) ,
            CustomerReceivables::DOUBTFUL            => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUE =>  2650 ]) ,
            CustomerReceivables::DAYS_LATE           => new ObservationSeries([ Oihana::UNIT_CODE => 'DAY' , Oihana::VALUE =>    98 ]) ,
            CustomerReceivables::NUMBER_OF_DOCUMENTS => 3 ,
        ]);

        $buckets = $receivables->overdue1To30->value
                 + $receivables->overdue31To60->value
                 + $receivables->overdue61To90->value
                 + $receivables->overdueOver90->value ;

        $this->assertSame( $receivables->overdue->value , $buckets );
        $this->assertLessThanOrEqual( $receivables->overdue->value , $receivables->doubtful->value );
        $this->assertSame( 'DAY' , $receivables->daysLate->unitCode );
        $this->assertSame( 3     , $receivables->numberOfDocuments );
    }

    public function testASubjectAlsoReadsAsABareCode(): void
    {
        $receivables = new CustomerReceivables([ CustomerReceivables::ABOUT => '369980' ]);

        $this->assertSame( '369980' , $receivables->about );
    }

    /**
     * A snapshot has no year and no step ; a record with nothing in doubt and
     * nothing to say about days late leaves those absent — never zero.
     */
    public function testWhatIsNotSetIsNotSerialized(): void
    {
        $receivables = new CustomerReceivables
        ([
            CustomerReceivables::OBSERVATION_DATE => '2026-09-22' ,
            CustomerReceivables::OVERDUE          => new ObservationSeries([ Oihana::UNIT_CODE => 'EUR' , Oihana::VALUE => 11150 ]) ,
        ]);

        $document = json_decode( json_encode( $receivables ) , true );

        $this->assertSame( 'CustomerReceivables' , $document[ '@type' ] );
        $this->assertSame( '2026-09-22' , $document[ CustomerReceivables::OBSERVATION_DATE ] );
        $this->assertSame( 11150 , $document[ CustomerReceivables::OVERDUE ][ Oihana::VALUE ] );
        $this->assertArrayNotHasKey( CustomerReceivables::YEAR               , $document );
        $this->assertArrayNotHasKey( CustomerReceivables::OBSERVATION_PERIOD , $document );
        $this->assertArrayNotHasKey( CustomerReceivables::DOUBTFUL           , $document );
        $this->assertArrayNotHasKey( CustomerReceivables::DAYS_LATE          , $document );
        $this->assertArrayNotHasKey( CustomerReceivables::NUMBER_OF_DOCUMENTS , $document );
        $this->assertArrayNotHasKey( CustomerReceivables::ASSIGNED_SELLER    , $document );
    }

    /**
     * The seven measures of what is owed and late.
     */
    private const array MEASURES =
    [
        CustomerReceivables::DAYS_LATE        ,
        CustomerReceivables::DOUBTFUL         ,
        CustomerReceivables::OVERDUE          ,
        CustomerReceivables::OVERDUE_1_TO_30  ,
        CustomerReceivables::OVERDUE_31_TO_60 ,
        CustomerReceivables::OVERDUE_61_TO_90 ,
        CustomerReceivables::OVERDUE_OVER_90  ,
    ];
}
