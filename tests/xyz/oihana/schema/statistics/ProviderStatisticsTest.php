<?php

namespace tests\xyz\oihana\schema\statistics ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\organizations\Provider;
use xyz\oihana\schema\statistics\ObservationSeries;
use xyz\oihana\schema\statistics\ProviderStatistics;
use xyz\oihana\schema\statistics\Statistics;

class ProviderStatisticsTest extends TestCase
{
    public function testIsAStatisticsRecord(): void
    {
        $this->assertInstanceOf( Statistics::class , new ProviderStatistics() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , ProviderStatistics::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionNamesTheSupplier(): void
    {
        $statistics = new Reflection()->hydrate
        (
            [ ProviderStatistics::ABOUT => [ 'id' => '100122' , 'name' => 'Northern Sawmill' ] ],
            ProviderStatistics::class
        );

        $this->assertInstanceOf( Provider::class , $statistics->about );
        $this->assertSame( '100122' , $statistics->about->id );
    }

    /**
     * @throws ReflectionException
     */
    public function testAPurchaseRecordLeavesOutWhatItDoesNotMeasure(): void
    {
        $statistics = new Reflection()->hydrate
        (
            [
                ProviderStatistics::ABOUT         => '100122' ,
                ProviderStatistics::DIRECTION     => BusinessDocumentDirection::PURCHASE ,
                ProviderStatistics::YEAR          => 2025 ,
                ProviderStatistics::PURCHASE_COST => [ 'unitCode' => 'EUR' , 'value' => 136116.99 ] ,
            ],
            ProviderStatistics::class
        );

        $this->assertInstanceOf( ObservationSeries::class , $statistics->purchaseCost );

        $document = json_decode( json_encode( $statistics ) , true );

        $this->assertSame( 'ProviderStatistics' , $document[ '@type' ] );
        $this->assertSame( 136116.99 , $document[ ProviderStatistics::PURCHASE_COST ][ Oihana::VALUE ] );
        $this->assertArrayNotHasKey( ProviderStatistics::REVENUE      , $document );
        $this->assertArrayNotHasKey( ProviderStatistics::GROSS_MARGIN , $document );
    }

    public function testItCarriesNoDimensionOfItsOwn(): void
    {
        $this->assertFalse( property_exists( ProviderStatistics::class , 'assignedSeller' ) );
        $this->assertFalse( property_exists( ProviderStatistics::class , 'assignedPOS' ) );
    }
}
