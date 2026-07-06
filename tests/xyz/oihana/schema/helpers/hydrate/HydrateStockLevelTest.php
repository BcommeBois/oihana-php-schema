<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\products\StockLevel;

use function xyz\oihana\schema\helpers\hydrate\hydrateStockLevel;

final class HydrateStockLevelTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesTheAssignedPointOfSale(): void
    {
        $level = hydrateStockLevel(
        [
            'value'       => 120 ,
            'assignedPOS' => [ 'name' => 'Bayonne' ] ,
        ]) ;

        $this->assertInstanceOf( StockLevel::class , $level ) ;
        $this->assertInstanceOf( Warehouse::class , $level->assignedPOS ) ;
        $this->assertSame( 'Bayonne' , $level->assignedPOS->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testKeepsAMinimalLevelUntouched(): void
    {
        $level = hydrateStockLevel( [ 'value' => 120 ] ) ;

        $this->assertInstanceOf( StockLevel::class , $level ) ;
        $this->assertSame( 120 , $level->value ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsNullOnAnUnsupportedInput(): void
    {
        $this->assertNull( hydrateStockLevel() ) ;
        $this->assertNull( hydrateStockLevel( 'raw' ) ) ;
    }
}
