<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\DefinedTerm;
use org\schema\GeoCoordinates;
use org\schema\PostalAddress;
use org\schema\PropertyValue;

use xyz\oihana\schema\places\CustomerSite;

use function xyz\oihana\schema\helpers\hydrate\hydrateCustomerSite;

final class HydrateCustomerSiteTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesASingleSiteWithItsNestedReferences(): void
    {
        $site = hydrateCustomerSite(
        [
            'name'               => 'Chantier A' ,
            'additionalProperty' => [ [ 'propertyID' => 'access' , 'value' => 'crane' ] ] ,
            'address'            => [ 'streetAddress' => '13 allée Gabrielle Dorziat' ] ,
            'geo'                => [ 'latitude' => 43.4696 , 'longitude' => -1.5531 ] ,
            'deliveryMethod'     => [ 'name' => 'Express' ] ,
        ]) ;

        $this->assertInstanceOf( CustomerSite::class , $site ) ;
        $this->assertContainsOnlyInstancesOf( PropertyValue::class , $site->additionalProperty ) ;
        $this->assertInstanceOf( PostalAddress::class  , $site->address ) ;
        $this->assertInstanceOf( GeoCoordinates::class , $site->geo ) ;
        $this->assertInstanceOf( DefinedTerm::class    , $site->deliveryMethod ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testKeepsAMinimalSiteUntouched(): void
    {
        $site = hydrateCustomerSite( [ 'name' => 'Chantier A' ] ) ;

        $this->assertInstanceOf( CustomerSite::class , $site ) ;
        $this->assertSame( 'Chantier A' , $site->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $sites = hydrateCustomerSite(
        [
            [ 'name' => 'Chantier A' ] ,
            [ 'name' => 'Chantier B' ] ,
        ]) ;

        $this->assertIsArray( $sites ) ;
        $this->assertCount( 2 , $sites ) ;
        $this->assertContainsOnlyInstancesOf( CustomerSite::class , $sites ) ;

        $this->assertNull( hydrateCustomerSite( [ 'raw' ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateCustomerSite() ) ;
        $this->assertSame( 'raw' , hydrateCustomerSite( 'raw' ) ) ;
    }
}
