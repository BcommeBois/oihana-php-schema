<?php

namespace tests\xyz\oihana\schema\statistics ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\products\Product;
use xyz\oihana\schema\statistics\ObservationSeries;
use xyz\oihana\schema\statistics\ProductStatistics;
use xyz\oihana\schema\statistics\Statistics;

class ProductStatisticsTest extends TestCase
{
    public function testIsAStatisticsRecord(): void
    {
        $this->assertInstanceOf( Statistics::class , new ProductStatistics() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , ProductStatistics::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionNamesTheArticle(): void
    {
        $statistics = new Reflection()->hydrate
        (
            [ ProductStatistics::ABOUT => [ 'id' => '105630' , 'name' => 'Oak flooring 180 x 12' ] ],
            ProductStatistics::class
        );

        $this->assertInstanceOf( Product::class , $statistics->about );
        $this->assertSame( '105630' , $statistics->about->id );
    }

    /**
     * The one family where a quantity is homogeneous, and therefore worth a total.
     *
     * @throws ReflectionException
     */
    public function testAQuantityCarriesBothItsTotalAndItsRun(): void
    {
        $statistics = new Reflection()->hydrate
        (
            [
                ProductStatistics::ABOUT     => '105630' ,
                ProductStatistics::DIRECTION => BusinessDocumentDirection::SALE ,
                ProductStatistics::YEAR      => 2025 ,
                ProductStatistics::QUANTITY  =>
                [
                    'unitCode' => 'MTK' ,
                    'value'    => 1200.5 ,
                    'values'   => [ 100.5 , 200 , 300 , 600 ] ,
                ],
            ],
            ProductStatistics::class
        );

        $this->assertInstanceOf( ObservationSeries::class , $statistics->quantity );
        $this->assertSame( 1200.5 , $statistics->quantity->value );
        $this->assertSame( 'MTK'  , $statistics->quantity->unitCode );
        $this->assertCount( 4 , $statistics->quantity->values );
    }

    public function testItCarriesNoDimensionOfItsOwn(): void
    {
        $this->assertFalse( property_exists( ProductStatistics::class , 'assignedSeller' ) );
        $this->assertFalse( property_exists( ProductStatistics::class , 'category' ) );
    }
}
