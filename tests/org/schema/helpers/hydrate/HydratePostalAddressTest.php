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
    public function testHydratesAddressDepartment(): void
    {
        $address = hydratePostalAddress(
        [
            'streetAddress'     => '20 Rue Mably' ,
            'addressDepartment' => 'Gironde' ,
        ]) ;

        $this->assertInstanceOf( PostalAddress::class , $address ) ;
        $this->assertSame( 'Gironde' , $address->addressDepartment ) ;
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
    public function testKeepsBareReferencesAndNullifiesAnEmptyResult(): void
    {
        $this->assertSame( [ 'address-ref-42' ] , hydratePostalAddress( [ 'address-ref-42' ] ) ) ;

        // An entry that WAS an array and gave nothing is the one that is dropped : an empty
        // address normalizes to nothing.
        $this->assertNull( hydratePostalAddress( [ [] ] ) ) ;
        $this->assertNull( hydratePostalAddress( [ null ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydratePostalAddress() ) ;
        $this->assertSame( 'raw' , hydratePostalAddress( 'raw' ) ) ;
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
        $bare = hydratePostalAddress( [ 'address-ref-42' , 'address-ref-42' ] ) ;

        $this->assertSame( [ 'address-ref-42' , 'address-ref-42' ] , $bare ) ;

        $mixed = hydratePostalAddress( [ 'address-ref-42' , [ 'streetAddress' => '1 Example street' ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 'address-ref-42' , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( PostalAddress::class , $mixed[ 1 ] ) ;
    }
}
