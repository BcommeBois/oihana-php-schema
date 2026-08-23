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

        // A bare reference is kept — in a list as much as on its own. Only an entry that
        // resolved to nothing is dropped.
        $this->assertSame( [ 'customer-ref-42' ] , hydrateCustomer( [ 'customer-ref-42' ] ) ) ;
        $this->assertNull( hydrateCustomer( [ null ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateCustomer() ) ;
        $this->assertSame( 'raw' , hydrateCustomer( 'raw' ) ) ;
    }

    /**
     * `contactPoint` may hold a lone instance or an unresolved reference, not
     * only a list. Both used to reach `hydrateContactPoint()`'s `?array`
     * parameter and raise a TypeError — from an ordinary payload, at that.
     *
     * @throws ReflectionException
     */
    public function testSurvivesAContactPointThatIsNotAList(): void
    {
        $contact = new ContactPoint( [ 'telephone' => '06 33 67 42 07' ] ) ;

        $fromInstance = hydrateCustomer( [ 'name' => 'ACME' , 'contactPoint' => $contact ] ) ;
        $fromString   = hydrateCustomer( [ 'name' => 'ACME' , 'contactPoint' => 'contact-ref-42' ] ) ;

        $this->assertInstanceOf( Customer::class , $fromInstance ) ;
        $this->assertInstanceOf( Customer::class , $fromString   ) ;

        // Neither shape is rewritten : the helper leaves what it cannot hydrate.
        $this->assertSame( $contact         , $fromInstance->contactPoint ) ;
        $this->assertSame( 'contact-ref-42' , $fromString->contactPoint   ) ;
    }

    /**
     * An empty list is not a list of things : it says "nothing here". Both nested
     * references must answer the same through the customer as through the nested
     * helper called on its own, otherwise the same fact takes two shapes depending
     * on the path taken.
     *
     * @throws ReflectionException
     */
    public function testEmptyNestedListsYieldNullNotAnEmptyArray(): void
    {
        $customer = hydrateCustomer
        ([
            'name'         => 'ACME' ,
            'contactPoint' => [] ,
            'address'      => [] ,
        ]) ;

        $this->assertInstanceOf( Customer::class , $customer ) ;

        $this->assertNull( $customer->contactPoint ) ;
        $this->assertNull( $customer->address      ) ;
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
        $bare = hydrateCustomer( [ 'customer-ref-42' , 'customer-ref-42' ] ) ;

        $this->assertSame( [ 'customer-ref-42' , 'customer-ref-42' ] , $bare ) ;

        $mixed = hydrateCustomer( [ 'customer-ref-42' , [ 'name' => 'Acme Corporation' ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 'customer-ref-42' , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( Customer::class , $mixed[ 1 ] ) ;
    }
}
