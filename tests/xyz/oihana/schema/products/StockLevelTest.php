<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\QuantitativeValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\products\StockLevel;

class StockLevelTest extends TestCase
{
    public function testIsQuantitativeValue(): void
    {
        $this->assertInstanceOf( QuantitativeValue::class , new StockLevel() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , StockLevel::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testFromArrayCreatesAStockLevel(): void
    {
        $level = StockLevel::fromArray([ 'value' => 24 , 'lastStockEntry' => '2026-01-12T14:32:00Z' ]) ;

        $this->assertInstanceOf( StockLevel::class , $level ) ;
        $this->assertSame( 24 , $level->value ) ;
        $this->assertSame( '2026-01-12T14:32:00Z' , $level->lastStockEntry ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testFromArrayHydratesTheAssignedPOSAsWarehouse(): void
    {
        $level = StockLevel::fromArray([ 'value' => 24 , 'assignedPOS' => [ 'name' => 'Bayonne' ] ]) ;

        $this->assertInstanceOf( Warehouse::class , $level->assignedPOS ) ;
        $this->assertSame( 'Bayonne' , $level->assignedPOS->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testFromArrayKeepsAWarehouseAssignedPOS(): void
    {
        $warehouse = new Warehouse([ 'name' => 'Bayonne' ]) ;

        $level = StockLevel::fromArray([ 'value' => 24 , 'assignedPOS' => $warehouse ]) ;

        $this->assertSame( $warehouse , $level->assignedPOS ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testFromArrayReturnsNullOnNull(): void
    {
        $this->assertNull( StockLevel::fromArray( null ) ) ;
    }
}
