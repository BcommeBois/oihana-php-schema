<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\DefinedTerm;
use org\schema\GeoCoordinates;
use org\schema\PostalAddress;
use org\schema\PropertyValue;

use xyz\oihana\schema\places\CustomerSite;
use xyz\oihana\schema\shipping\DeliveryRouteAssignment;
use xyz\oihana\schema\thesaurus\DeliveryMethodTerm;
use xyz\oihana\schema\thesaurus\DeliveryRouteTerm;

use function xyz\oihana\schema\helpers\hydrate\hydrateCustomerSite;
use function xyz\oihana\schema\helpers\hydrate\hydrateDeliveryRouteAssignment;

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
     * The site's `deliveryMethod` hydrates into the enriched `DeliveryMethodTerm`
     * subclass — not the plain schema.org `DefinedTerm` — so `shippingRate` and
     * `freeShippingThreshold` survive the hydration.
     *
     * @throws ReflectionException
     */
    public function testHydratesDeliveryMethodAsADeliveryMethodTerm(): void
    {
        $site = hydrateCustomerSite(
        [
            'name'           => 'Chantier A' ,
            'deliveryMethod' =>
            [
                'name'                                      => 'Free above 1000, otherwise 39' ,
                DeliveryMethodTerm::SHIPPING_RATE           => 39 ,
                DeliveryMethodTerm::FREE_SHIPPING_THRESHOLD => 1000 ,
            ],
        ]) ;

        $this->assertInstanceOf( DeliveryMethodTerm::class , $site->deliveryMethod ) ;
        $this->assertSame( 39   , $site->deliveryMethod->shippingRate ) ;
        $this->assertSame( 1000 , $site->deliveryMethod->freeShippingThreshold ) ;
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

    /**
     * The routes serving the address come out as assignments, each resolving its
     * own route reference — a joined row into a term, a bare code left alone.
     *
     * @throws ReflectionException
     */
    public function testHydratesDeliveryRouteAsAssignments(): void
    {
        $site = hydrateCustomerSite
        ([
            'name'          => 'Chantier ouest' ,
            'deliveryRoute' =>
            [
                [
                    DeliveryRouteAssignment::ROUTE    => [ 'id' => '01D' , 'name' => 'Ouest, milieu de semaine' ] ,
                    DeliveryRouteAssignment::POSITION => 12 ,
                ],
                [ DeliveryRouteAssignment::ROUTE => '03' ] ,
            ],
        ]) ;

        $this->assertInstanceOf( CustomerSite::class , $site ) ;

        $this->assertIsArray( $site->deliveryRoute ) ;
        $this->assertCount( 2 , $site->deliveryRoute ) ;
        $this->assertContainsOnlyInstancesOf( DeliveryRouteAssignment::class , $site->deliveryRoute ) ;

        $this->assertInstanceOf( DeliveryRouteTerm::class , $site->deliveryRoute[ 0 ]->route ) ;
        $this->assertSame( 'Ouest, milieu de semaine' , $site->deliveryRoute[ 0 ]->route->name ) ;
        $this->assertSame( 12 , $site->deliveryRoute[ 0 ]->position ) ;

        $this->assertSame( '03' , $site->deliveryRoute[ 1 ]->route ) ;
    }

    /**
     * An empty list is not a list of things : it says "nothing here". Every nested
     * reference must answer the same through the site as through the nested helper
     * called on its own, otherwise the same fact takes two shapes depending on the
     * path taken, and a consumer iterating the result has to handle both.
     *
     * @throws ReflectionException
     */
    public function testEmptyNestedListsYieldNullNotAnEmptyArray(): void
    {
        $site = hydrateCustomerSite
        ([
            'name'               => 'Chantier vide' ,
            'additionalProperty' => [] ,
            'address'            => [] ,
            'geo'                => [] ,
            'deliveryMethod'     => [] ,
            'deliveryRoute'      => [] ,
        ]) ;

        $this->assertInstanceOf( CustomerSite::class , $site ) ;

        $this->assertNull( $site->additionalProperty ) ;
        $this->assertNull( $site->address            ) ;
        $this->assertNull( $site->geo                ) ;
        $this->assertNull( $site->deliveryMethod     ) ;
        $this->assertNull( $site->deliveryRoute      ) ;

        // Same answer through the site as through the nested helper on its own.
        $this->assertSame
        (
            hydrateDeliveryRouteAssignment( [] ) ,
            $site->deliveryRoute
        ) ;
    }

    /**
     * A reference nobody joined yet stays what it was : hydration reads what the
     * payload holds, it never rewrites a value it cannot resolve.
     *
     * @throws ReflectionException
     */
    public function testLeavesUnresolvedReferencesUntouched(): void
    {
        $address = new PostalAddress( [ 'streetAddress' => '13 allée Gabrielle Dorziat' ] ) ;

        $site = hydrateCustomerSite
        ([
            'name'           => 'Chantier référencé' ,
            'address'        => $address ,
            'deliveryMethod' => 'delivery-method-ref-42' ,
        ]) ;

        $this->assertInstanceOf( CustomerSite::class , $site ) ;

        $this->assertSame( $address                , $site->address        ) ;
        $this->assertSame( 'delivery-method-ref-42' , $site->deliveryMethod ) ;
    }

    /**
     * Hydration resolves what the payload carries ; it does not invent properties the
     * payload never mentioned. A site that says nothing about its geo, its delivery
     * method or its routes comes out with those properties left uninitialized, and so
     * absent from the serialized shape — not materialized to `null`.
     *
     * @throws ReflectionException
     */
    public function testDoesNotMaterializePropertiesThePayloadNeverCarried(): void
    {
        $site = hydrateCustomerSite( [ 'name' => 'Chantier minimal' ] ) ;

        $this->assertInstanceOf( CustomerSite::class , $site ) ;

        $serialized = $site->jsonSerialize() ;

        $this->assertArrayNotHasKey( 'geo'            , $serialized ) ;
        $this->assertArrayNotHasKey( 'deliveryMethod' , $serialized ) ;
        $this->assertArrayNotHasKey( 'deliveryRoute'  , $serialized ) ;
    }
}
