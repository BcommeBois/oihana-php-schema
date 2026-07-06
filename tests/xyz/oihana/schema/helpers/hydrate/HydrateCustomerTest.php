<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\ContactPoint;
use org\schema\PostalAddress;

use xyz\oihana\schema\organizations\Customer;

use function xyz\oihana\schema\helpers\hydrate\hydrateCustomer;

final class HydrateCustomerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesASingleCustomerWithItsNestedReferences(): void
    {
        $customer = hydrateCustomer(
        [
            'name'         => '1000 BOUTS DE BOIS' ,
            'contactPoint' => [ [ 'telephone' => '06 33 67 42 07' ] ] ,
            'address'      => [ 'streetAddress' => 'Le Mazel' , 'postalCode' => '12430' ] ,
        ]) ;

        $this->assertInstanceOf( Customer::class , $customer ) ;
        $this->assertIsArray( $customer->contactPoint ) ;
        $this->assertContainsOnlyInstancesOf( ContactPoint::class , $customer->contactPoint ) ;
        $this->assertInstanceOf( PostalAddress::class , $customer->address ) ;
        $this->assertSame( 'Le Mazel' , $customer->address->streetAddress ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testKeepsAMinimalCustomerUntouched(): void
    {
        $customer = hydrateCustomer( [ 'name' => '2C BOIS' ] ) ;

        $this->assertInstanceOf( Customer::class , $customer ) ;
        $this->assertSame( '2C BOIS' , $customer->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $customers = hydrateCustomer(
        [
            [ 'name' => '2C BOIS' ] ,
            [ 'name' => '3D BOIS' ] ,
        ]) ;

        $this->assertIsArray( $customers ) ;
        $this->assertCount( 2 , $customers ) ;
        $this->assertContainsOnlyInstancesOf( Customer::class , $customers ) ;

        $this->assertNull( hydrateCustomer( [ 'raw' ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateCustomer() ) ;
        $this->assertSame( 'raw' , hydrateCustomer( 'raw' ) ) ;
    }
}
