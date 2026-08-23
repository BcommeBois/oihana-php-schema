<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use org\schema\DefinedTerm;
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
            'jobTitle'           => [ 'id' => 1 , 'name' => 'Gérant' ] ,
            'workLocation'       => [ 'name' => 'Chantier A' ] ,
        ]) ;

        $this->assertInstanceOf( CustomerEmployee::class , $employee ) ;
        $this->assertContainsOnlyInstancesOf( PropertyValue::class , $employee->additionalProperty ) ;
        $this->assertContainsOnlyInstancesOf( ContactPoint::class  , $employee->contactPoint ) ;
        $this->assertInstanceOf( DefinedTerm::class  , $employee->jobTitle ) ;
        $this->assertInstanceOf( CustomerSite::class , $employee->workLocation ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testLeavesABareJobTitleCodeUntouched(): void
    {
        $employee = hydrateCustomerEmployee( [ 'name' => 'Jean Dupont' , 'jobTitle' => 1 ] ) ;
        $this->assertSame( 1 , $employee->jobTitle ) ;
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

        // A bare reference is kept — in a list as much as on its own. Only an entry that
        // resolved to nothing is dropped.
        $this->assertSame( [ 'employee-ref-42' ] , hydrateCustomerEmployee( [ 'employee-ref-42' ] ) ) ;
        $this->assertNull( hydrateCustomerEmployee( [ null ] ) ) ;
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
        $bare = hydrateCustomerEmployee( [ 'employee-ref-42' , 'employee-ref-42' ] ) ;

        $this->assertSame( [ 'employee-ref-42' , 'employee-ref-42' ] , $bare ) ;

        $mixed = hydrateCustomerEmployee( [ 'employee-ref-42' , [ 'name' => 'Jane Doe' ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 'employee-ref-42' , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( CustomerEmployee::class , $mixed[ 1 ] ) ;
    }
}
