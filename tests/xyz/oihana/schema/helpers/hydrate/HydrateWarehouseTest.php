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

        // A bare reference is kept — in a list as much as on its own. Only an entry that
        // resolved to nothing is dropped.
        $this->assertSame( [ 'warehouse-ref-42' ] , hydrateWarehouse( [ 'warehouse-ref-42' ] ) ) ;
        $this->assertNull( hydrateWarehouse( [ null ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateWarehouse() ) ;
        $this->assertSame( 'raw' , hydrateWarehouse( 'raw' ) ) ;
    }

    /**
     * 🔑 **A bare reference survives inside a list**, exactly as it does on its own — the
     * contract every helper of the family states in its header, applied entry by entry.
     * A property that stores handles rather than resolved objects used to read back `null`.
     *
     * The keys matter as much as the contents : a filtered list left with gaps serializes
     * as a JSON **object**, and a consumer walking the value gets something it cannot walk.
     *
     * @throws ReflectionException
     */
    public function testAListOfReferencesSurvivesAndKeepsItsKeys(): void
    {
        $bare = hydrateWarehouse( [ 'warehouse-ref-42' , 'warehouse-ref-42' ] ) ;

        $this->assertSame( [ 'warehouse-ref-42' , 'warehouse-ref-42' ] , $bare ) ;

        $mixed = hydrateWarehouse( [ 'warehouse-ref-42' , [ 'name' => 'Head office' ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 'warehouse-ref-42' , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( Warehouse::class , $mixed[ 1 ] ) ;
    }
}
