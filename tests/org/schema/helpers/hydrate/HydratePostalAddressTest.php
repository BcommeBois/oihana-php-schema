<?php

namespace tests\org\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\PostalAddress;

use function org\schema\helpers\hydrate\hydratePostalAddress;

final class HydratePostalAddressTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesASingleDefinitionAndNormalizesEmptyValues(): void
    {
        $address = hydratePostalAddress(
        [
            'streetAddress'   => '20 Rue Mably' ,
            'postalCode'      => '33000' ,
            'addressLocality' => null ,
        ]) ;

        $this->assertInstanceOf( PostalAddress::class , $address ) ;
        $this->assertSame( '20 Rue Mably' , $address->streetAddress ) ;
        $this->assertNull( $address->addressLocality ?? null ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayOfDefinitions(): void
    {
        $addresses = hydratePostalAddress(
        [
            [ 'streetAddress' => '20 Rue Mably' ] ,
            [ 'streetAddress' => '13 Boulevard des Capucines' ] ,
        ]) ;

        $this->assertIsArray( $addresses ) ;
        $this->assertCount( 2 , $addresses ) ;
        $this->assertContainsOnlyInstancesOf( PostalAddress::class , $addresses ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testFiltersNonAddressEntriesAndNullifiesAnEmptyResult(): void
    {
        $this->assertNull( hydratePostalAddress( [ 'foo' ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydratePostalAddress() ) ;
        $this->assertSame( 'raw' , hydratePostalAddress( 'raw' ) ) ;
    }
}
