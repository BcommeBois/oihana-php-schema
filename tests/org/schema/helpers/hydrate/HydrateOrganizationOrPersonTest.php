<?php

namespace tests\org\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\Organization;
use org\schema\Person;
use org\schema\PostalAddress;

use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\people\CustomerEmployee;

use function org\schema\helpers\hydrate\hydrateOrganizationOrPerson;

final class HydrateOrganizationOrPersonTest extends TestCase
{
    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAPersonWhenTheTypeSaysSo(): void
    {
        $entity = hydrateOrganizationOrPerson( [ '@type' => 'Person' , 'name' => 'Jean Dupont' ] ) ;

        $this->assertInstanceOf( Person::class , $entity ) ;
        $this->assertSame( 'Jean Dupont' , $entity->name ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAPersonWhateverTheTypeCase(): void
    {
        $this->assertInstanceOf( Person::class , hydrateOrganizationOrPerson( [ '@type' => 'person' ] ) ) ;
        $this->assertInstanceOf( Person::class , hydrateOrganizationOrPerson( [ '@type' => 'PERSON' ] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnOrganizationByDefault(): void
    {
        $entity = hydrateOrganizationOrPerson( [ 'name' => 'ACME' ] ) ;

        $this->assertInstanceOf( Organization::class , $entity ) ;
        $this->assertSame( 'ACME' , $entity->name ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnOrganizationOnAnExplicitOrAnySubtype(): void
    {
        $this->assertInstanceOf( Organization::class , hydrateOrganizationOrPerson( [ '@type' => 'Organization'  ] ) ) ;
        $this->assertInstanceOf( Organization::class , hydrateOrganizationOrPerson( [ '@type' => 'Corporation'   ] ) ) ;
        $this->assertInstanceOf( Organization::class , hydrateOrganizationOrPerson( [ '@type' => 'LocalBusiness' ] ) ) ;
        $this->assertInstanceOf( Organization::class , hydrateOrganizationOrPerson( [ '@type' => 12345           ] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesTheNestedAddress(): void
    {
        $entity = hydrateOrganizationOrPerson
        ([
            '@type'  => 'Person' ,
            'name'   => 'Jean Dupont' ,
            'address' => [ 'streetAddress' => '20 Rue Mably' , 'postalCode' => '33000' ] ,
        ]) ;

        $this->assertInstanceOf( PostalAddress::class , $entity->address ) ;
        $this->assertSame( '20 Rue Mably' , $entity->address->streetAddress ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesIntoCustomBusinessSubtypes(): void
    {
        $customer = hydrateOrganizationOrPerson
        (
            [ 'name' => 'ACME' ] ,
            Customer::class ,
            CustomerEmployee::class
        ) ;

        $this->assertInstanceOf( Customer::class , $customer ) ;
        $this->assertSame( 'ACME' , $customer->name ) ;

        $employee = hydrateOrganizationOrPerson
        (
            [ '@type' => 'Person' , 'name' => 'Jean Dupont' ] ,
            Customer::class ,
            CustomerEmployee::class
        ) ;

        $this->assertInstanceOf( CustomerEmployee::class , $employee ) ;
        $this->assertSame( 'Jean Dupont' , $employee->name ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $entities = hydrateOrganizationOrPerson
        ([
            [ '@type' => 'Person'       , 'name' => 'Jean Dupont' ] ,
            [ '@type' => 'Organization' , 'name' => 'ACME'        ] ,
        ]) ;

        $this->assertIsArray( $entities ) ;
        $this->assertCount( 2 , $entities ) ;

        $this->assertInstanceOf( Person::class       , $entities[ 0 ] ) ;
        $this->assertInstanceOf( Organization::class , $entities[ 1 ] ) ;

        $this->assertNull( hydrateOrganizationOrPerson( [] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAnAlreadyHydratedEntity(): void
    {
        $person = new Person( [ 'name' => 'Jean Dupont' ] ) ;

        $this->assertSame( $person , hydrateOrganizationOrPerson( $person ) ) ;
        $this->assertSame( [ $person ] , hydrateOrganizationOrPerson( [ $person ] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateOrganizationOrPerson() ) ;
        $this->assertSame( 'raw' , hydrateOrganizationOrPerson( 'raw' ) ) ;
    }
}
