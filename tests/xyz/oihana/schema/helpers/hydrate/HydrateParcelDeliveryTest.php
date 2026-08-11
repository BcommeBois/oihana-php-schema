<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\DefinedTerm;
use org\schema\Organization;
use org\schema\ParcelDelivery;
use org\schema\Person;
use org\schema\PostalAddress;

use xyz\oihana\schema\thesaurus\DeliveryMethodTerm;
use xyz\oihana\schema\thesaurus\DeliveryRouteTerm;

use function xyz\oihana\schema\helpers\hydrate\hydrateParcelDelivery;

final class HydrateParcelDeliveryTest extends TestCase
{
    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesADeliveryWithItsAddressesAndTravelTerms(): void
    {
        $delivery = hydrateParcelDelivery(
        [
            'deliveryAddress' =>
            [
                'streetAddress'   => '8 Rue Paul Gros' ,
                'postalCode'      => '33270' ,
                'addressLocality' => 'FLOIRAC' ,
                'telephone'       => '05 57 77 13 30' ,
            ] ,
            'originAddress' =>
            [
                'streetAddress'   => '4105 Avenue de Bordeaux' ,
                'postalCode'      => '33127' ,
            ] ,
            'hasDeliveryMethod'       => [ 'id' => 'F13' , 'name' => 'Franco de port'         ] ,
            'hasDeliveryRoute'        => [ 'id' => '54'  , 'name' => 'Libourne / rive droite' ] ,
            'requestedDeliveryDate'   => '2026-08-10' ,
            'trackingNumber'          => 'BCB-42' ,
        ]) ;

        $this->assertInstanceOf( ParcelDelivery::class , $delivery ) ;

        $this->assertInstanceOf( PostalAddress::class , $delivery->deliveryAddress ) ;
        $this->assertSame( '8 Rue Paul Gros' , $delivery->deliveryAddress->streetAddress ) ;
        $this->assertSame( 'FLOIRAC'         , $delivery->deliveryAddress->addressLocality ) ;
        $this->assertSame( '05 57 77 13 30'  , $delivery->deliveryAddress->telephone ) ;

        $this->assertInstanceOf( PostalAddress::class , $delivery->originAddress ) ;
        $this->assertSame( '33127' , $delivery->originAddress->postalCode ) ;

        $this->assertInstanceOf( DeliveryMethodTerm::class , $delivery->hasDeliveryMethod ) ;
        $this->assertSame( 'F13' , $delivery->hasDeliveryMethod->id ) ;

        $this->assertInstanceOf( DeliveryRouteTerm::class , $delivery->hasDeliveryRoute ) ;
        $this->assertSame( '54' , $delivery->hasDeliveryRoute->id ) ;

        $this->assertSame( '2026-08-10' , $delivery->requestedDeliveryDate ) ;
        $this->assertSame( 'BCB-42'     , $delivery->trackingNumber        ) ;
    }

    /**
     * The two travel terms are parameters, not hard-wired classes : that is what
     * keeps the thesaurus out of the Schema.org class itself.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testTheTravelTermClassesCanBeReplaced(): void
    {
        $delivery = hydrateParcelDelivery
        (
            [
                'hasDeliveryMethod' => [ 'id' => 'LIV' ] ,
                'hasDeliveryRoute'  => [ 'id' => '03D' ] ,
            ] ,
            DefinedTerm::class ,
            DefinedTerm::class
        ) ;

        $this->assertInstanceOf( DefinedTerm::class , $delivery->hasDeliveryMethod ) ;
        $this->assertInstanceOf( DefinedTerm::class , $delivery->hasDeliveryRoute  ) ;

        $this->assertNotInstanceOf( DeliveryMethodTerm::class , $delivery->hasDeliveryMethod ) ;
        $this->assertNotInstanceOf( DeliveryRouteTerm::class  , $delivery->hasDeliveryRoute  ) ;
    }

    /**
     * The carrier carries the `Organization|Person` union the property type cannot
     * resolve on its own, so it is read from the payload's `@type`.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testResolvesTheProviderFromItsType(): void
    {
        $carrier = hydrateParcelDelivery( [ 'provider' => [ 'name' => 'Transports Dupont' ] ] ) ;

        $this->assertInstanceOf( Organization::class , $carrier->provider ) ;
        $this->assertSame( 'Transports Dupont' , $carrier->provider->name ) ;

        $driver = hydrateParcelDelivery( [ 'provider' => [ '@type' => 'Person' , 'familyName' => 'Dupont' ] ] ) ;

        $this->assertInstanceOf( Person::class , $driver->provider ) ;
        $this->assertSame( 'Dupont' , $driver->provider->familyName ) ;
    }

    /**
     * Nothing is resolved that is not an array : a reference already given as a
     * string, and a slot left empty, both travel as they came. `provider` is not of
     * the party — its declared type refuses a bare string outright, so a carrier is
     * either an object or absent.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testLeavesNonArrayReferencesAlone(): void
    {
        $delivery = hydrateParcelDelivery(
        [
            'deliveryAddress'   => '8 Rue Paul Gros, 33270 FLOIRAC' ,
            'hasDeliveryMethod' => 'F13' ,
            'hasDeliveryRoute'  => '54'  ,
        ]) ;

        $this->assertInstanceOf( ParcelDelivery::class , $delivery ) ;

        $this->assertSame( '8 Rue Paul Gros, 33270 FLOIRAC' , $delivery->deliveryAddress   ) ;
        $this->assertSame( 'F13'                            , $delivery->hasDeliveryMethod ) ;
        $this->assertSame( '54'                             , $delivery->hasDeliveryRoute  ) ;

        $this->assertNull( $delivery->originAddress ?? null ) ;
        $this->assertNull( $delivery->provider      ?? null ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $deliveries = hydrateParcelDelivery(
        [
            [ 'hasDeliveryMethod' => [ 'id' => 'SPL' ] ] ,
            [ 'hasDeliveryRoute'  => [ 'id' => '03D' ] ] ,
        ]) ;

        $this->assertIsArray( $deliveries ) ;
        $this->assertCount( 2 , $deliveries ) ;
        $this->assertContainsOnlyInstancesOf( ParcelDelivery::class , $deliveries ) ;

        $this->assertInstanceOf( DeliveryMethodTerm::class , $deliveries[ 0 ]->hasDeliveryMethod ) ;
        $this->assertInstanceOf( DeliveryRouteTerm::class  , $deliveries[ 1 ]->hasDeliveryRoute  ) ;

        $this->assertNull( hydrateParcelDelivery( [ 'raw' ] ) ) ;
        $this->assertNull( hydrateParcelDelivery( [] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAlreadyHydratedDeliveries(): void
    {
        $delivery = new ParcelDelivery( [ 'trackingNumber' => 'BCB-42' ] ) ;

        $this->assertSame( $delivery , hydrateParcelDelivery( $delivery ) ) ;
        $this->assertSame( [ $delivery ] , hydrateParcelDelivery( [ $delivery ] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateParcelDelivery() ) ;
        $this->assertSame( 'raw' , hydrateParcelDelivery( 'raw' ) ) ;
    }
}
