<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\PostalAddress;

use xyz\oihana\schema\organizations\Subsidiary;
use xyz\oihana\schema\places\Warehouse;

use function xyz\oihana\schema\helpers\hydrate\hydrateWarehouse;

final class HydrateWarehouseTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesTheOwningSubsidiary(): void
    {
        $warehouse = hydrateWarehouse(
        [
            'name'    => 'Bayonne' ,
            'ownedBy' => [ 'name' => 'South Branch' ] ,
        ]) ;

        $this->assertInstanceOf( Warehouse::class , $warehouse ) ;
        $this->assertInstanceOf( Subsidiary::class , $warehouse->ownedBy ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testKeepsAMinimalWarehouseUntouched(): void
    {
        $warehouse = hydrateWarehouse( [ 'name' => 'Bayonne' ] ) ;

        $this->assertInstanceOf( Warehouse::class , $warehouse ) ;
        $this->assertSame( 'Bayonne' , $warehouse->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesThePostalAddress(): void
    {
        $warehouse = hydrateWarehouse(
        [
            'name'    => 'Bayonne' ,
            'address' => [ 'addressLocality' => 'Bayonne' , 'postalCode' => '64100' ] ,
        ]) ;

        $this->assertInstanceOf( Warehouse::class , $warehouse ) ;
        $this->assertInstanceOf( PostalAddress::class , $warehouse->address ) ;
        $this->assertSame( 'Bayonne' , $warehouse->address->addressLocality ) ;
        $this->assertSame( '64100'   , $warehouse->address->postalCode ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $warehouses = hydrateWarehouse(
        [
            [ 'name' => 'Bayonne'  ] ,
            [ 'name' => 'Bordeaux' ] ,
        ]) ;

        $this->assertIsArray( $warehouses ) ;
        $this->assertCount( 2 , $warehouses ) ;
        $this->assertContainsOnlyInstancesOf( Warehouse::class , $warehouses ) ;

        $this->assertNull( hydrateWarehouse( [ 'raw' ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateWarehouse() ) ;
        $this->assertSame( 'raw' , hydrateWarehouse( 'raw' ) ) ;
    }
}
