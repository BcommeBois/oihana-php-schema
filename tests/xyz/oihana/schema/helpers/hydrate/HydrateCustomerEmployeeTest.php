<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\ContactPoint;
use org\schema\PropertyValue;

use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\places\CustomerSite;

use function xyz\oihana\schema\helpers\hydrate\hydrateCustomerEmployee;
use function xyz\oihana\schema\helpers\hydrate\hydrateCustomerSite;

final class HydrateCustomerEmployeeTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesASingleEmployeeWithItsNestedReferences(): void
    {
        $employee = hydrateCustomerEmployee(
        [
            'name'               => 'Jean Dupont' ,
            'additionalProperty' => [ [ 'propertyID' => 'civility' , 'value' => 'M.' ] ] ,
            'contactPoint'       => [ [ 'telephone' => '06 00 00 00 00' ] ] ,
            'workLocation'       => [ 'name' => 'Chantier A' ] ,
        ]) ;

        $this->assertInstanceOf( CustomerEmployee::class , $employee ) ;
        $this->assertContainsOnlyInstancesOf( PropertyValue::class , $employee->additionalProperty ) ;
        $this->assertContainsOnlyInstancesOf( ContactPoint::class  , $employee->contactPoint ) ;
        $this->assertInstanceOf( CustomerSite::class , $employee->workLocation ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testKeepsAMinimalEmployeeUntouched(): void
    {
        $employee = hydrateCustomerEmployee( [ 'name' => 'Jean Dupont' ] ) ;

        $this->assertInstanceOf( CustomerEmployee::class , $employee ) ;
        $this->assertSame( 'Jean Dupont' , $employee->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $employees = hydrateCustomerEmployee(
        [
            [ 'name' => 'Jean Dupont'   ] ,
            [ 'name' => 'Marie Martin'  ] ,
        ]) ;

        $this->assertIsArray( $employees ) ;
        $this->assertCount( 2 , $employees ) ;
        $this->assertContainsOnlyInstancesOf( CustomerEmployee::class , $employees ) ;

        $this->assertNull( hydrateCustomerEmployee( [ 'raw' ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateCustomerEmployee() ) ;
        $this->assertSame( 'raw' , hydrateCustomerEmployee( 'raw' ) ) ;
    }

    /**
     * An empty list is not a list of things : it says "nothing here". Every nested
     * reference must answer the same through the employee as through the nested
     * helper called on its own, otherwise the same fact takes two shapes depending
     * on the path taken.
     *
     * @throws ReflectionException
     */
    public function testEmptyNestedListsYieldNullNotAnEmptyArray(): void
    {
        $employee = hydrateCustomerEmployee
        ([
            'name'               => 'Jean Dupont' ,
            'additionalProperty' => [] ,
            'contactPoint'       => [] ,
            'workLocation'       => [] ,
        ]) ;

        $this->assertInstanceOf( CustomerEmployee::class , $employee ) ;

        $this->assertNull( $employee->additionalProperty ) ;
        $this->assertNull( $employee->contactPoint       ) ;
        $this->assertNull( $employee->workLocation       ) ;

        // Same answer through the employee as through the nested helper on its own.
        $this->assertSame( hydrateCustomerSite( [] ) , $employee->workLocation ) ;
    }

    /**
     * A reference nobody joined yet stays what it was : hydration reads what the
     * payload holds, it never rewrites a value it cannot resolve.
     *
     * @throws ReflectionException
     */
    public function testLeavesUnresolvedReferencesUntouched(): void
    {
        $employee = hydrateCustomerEmployee
        ([
            'name'         => 'Jean Dupont' ,
            'workLocation' => 'site-ref-42' ,
        ]) ;

        $this->assertInstanceOf( CustomerEmployee::class , $employee ) ;
        $this->assertSame( 'site-ref-42' , $employee->workLocation ) ;
    }
}
