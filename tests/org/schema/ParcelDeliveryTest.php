<?php

namespace tests\org\schema ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;
use org\schema\DefinedTerm;
use org\schema\enumerations\DeliveryMethod;
use org\schema\Intangible;
use org\schema\Organization;
use org\schema\ParcelDelivery;
use org\schema\Person;
use org\schema\PostalAddress;

class ParcelDeliveryTest extends TestCase
{
    public function testIsIntangible(): void
    {
        $this->assertInstanceOf( Intangible::class , new ParcelDelivery() );
    }

    public function testRequestedDeliveryDateDefaultsToNull(): void
    {
        $delivery = new ParcelDelivery();

        $this->assertNull( $delivery->requestedDeliveryDate ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorCopiesRequestedDeliveryDate(): void
    {
        $delivery = new ParcelDelivery( [ Schema::REQUESTED_DELIVERY_DATE => '2026-09-14' ] );

        $this->assertSame( '2026-09-14' , $delivery->requestedDeliveryDate );
    }

    public function testRequestedDeliveryDateAcceptsATimestamp(): void
    {
        $delivery = new ParcelDelivery();

        $delivery->requestedDeliveryDate = 1_789_000_000 ;

        $this->assertSame( 1_789_000_000 , $delivery->requestedDeliveryDate );
    }

    /**
     * The requested date is an intent, the expected window an estimate : both
     * live on the same delivery without either standing in for the other.
     * @throws ReflectionException
     */
    public function testRequestedDeliveryDateIsIndependentOfTheExpectedArrivalWindow(): void
    {
        $delivery = new ParcelDelivery
        ([
            Schema::REQUESTED_DELIVERY_DATE => '2026-09-14' ,
            Schema::EXPECTED_ARRIVAL_FROM   => '2026-09-16' ,
            Schema::EXPECTED_ARRIVAL_UNTIL  => '2026-09-18' ,
        ]);

        $this->assertSame( '2026-09-14' , $delivery->requestedDeliveryDate );
        $this->assertSame( '2026-09-16' , $delivery->expectedArrivalFrom   );
        $this->assertSame( '2026-09-18' , $delivery->expectedArrivalUntil  );
    }

    /**
     * A row read straight from storage hands back an associative array : the
     * constructor assigns it as-is, which the previous union rejected.
     * @throws ReflectionException
     */
    public function testDeliveryAddressAcceptsARawArray(): void
    {
        $delivery = new ParcelDelivery
        ([
            Schema::DELIVERY_ADDRESS => [ Schema::STREET_ADDRESS => '12 rue des Bois' ] ,
        ]);

        $this->assertIsArray( $delivery->deliveryAddress );
        $this->assertSame( '12 rue des Bois' , $delivery->deliveryAddress[ Schema::STREET_ADDRESS ] );
    }

    public function testDeliveryAddressStillAcceptsAPostalAddress(): void
    {
        $delivery = new ParcelDelivery();

        $delivery->deliveryAddress = new PostalAddress( [ Schema::STREET_ADDRESS => '12 rue des Bois' ] );

        $this->assertInstanceOf( PostalAddress::class , $delivery->deliveryAddress );
        $this->assertSame( '12 rue des Bois' , $delivery->deliveryAddress->streetAddress );
    }

    /**
     * The three other structured properties take a raw array too, for the same
     * reason : a base read hands one back, and the constructor assigns it as-is.
     *
     * @throws ReflectionException
     */
    public function testStructuredPropertiesAcceptARawArray(): void
    {
        $delivery = new ParcelDelivery
        ([
            Schema::ORIGIN_ADDRESS => [ Schema::STREET_ADDRESS => '1 quai des Docks' ] ,
            Schema::PART_OF_ORDER  => [ Schema::ORDER_NUMBER   => 'C-42'             ] ,
            Schema::PROVIDER       => [ Schema::NAME           => 'Transports Etchea' ] ,
        ]);

        $this->assertIsArray( $delivery->originAddress );
        $this->assertIsArray( $delivery->partOfOrder   );
        $this->assertIsArray( $delivery->provider      );

        $this->assertSame( '1 quai des Docks'  , $delivery->originAddress[ Schema::STREET_ADDRESS ] );
        $this->assertSame( 'C-42'              , $delivery->partOfOrder  [ Schema::ORDER_NUMBER   ] );
        $this->assertSame( 'Transports Etchea' , $delivery->provider     [ Schema::NAME           ] );
    }

    public function testHasDeliveryMethodAcceptsASchemaOrgEnumerationValue(): void
    {
        $delivery = new ParcelDelivery();

        $delivery->hasDeliveryMethod = DeliveryMethod::UPS ;

        $this->assertSame( DeliveryMethod::UPS , $delivery->hasDeliveryMethod );
    }

    /**
     * A back office keeps its own priced list of delivery methods : those terms
     * reach the union through {@see DefinedTerm}, without `org\schema` ever
     * having to know about the house thesaurus.
     */
    public function testHasDeliveryMethodAcceptsADefinedTerm(): void
    {
        $delivery = new ParcelDelivery();

        $term = new DefinedTerm();
        $term->name = 'Livraison express' ;

        $delivery->hasDeliveryMethod = $term ;

        $this->assertInstanceOf( DefinedTerm::class , $delivery->hasDeliveryMethod );
        $this->assertSame( $term , $delivery->hasDeliveryMethod );
    }

    public function testHasDeliveryMethodAcceptsAStringIdentifier(): void
    {
        $delivery = new ParcelDelivery();

        $delivery->hasDeliveryMethod = 'express' ;

        $this->assertSame( 'express' , $delivery->hasDeliveryMethod );
    }

    public function testHasDeliveryRouteDefaultsToNull(): void
    {
        $delivery = new ParcelDelivery();

        $this->assertNull( $delivery->hasDeliveryRoute ?? null );
    }

    /**
     * The route is orthogonal to the method : a dozen circuits may all be run
     * under the single "own fleet" method, so naming one says nothing about the
     * other and neither overwrites it.
     */
    public function testHasDeliveryRouteIsIndependentOfTheMethod(): void
    {
        $delivery = new ParcelDelivery();

        $delivery->hasDeliveryMethod = DeliveryMethod::OWN_FLEET ;
        $delivery->hasDeliveryRoute  = '01D' ;

        $this->assertSame( DeliveryMethod::OWN_FLEET , $delivery->hasDeliveryMethod );
        $this->assertSame( '01D'                     , $delivery->hasDeliveryRoute );
    }

    /**
     * The union accepts the bare reference a document freezes, as well as the
     * {@see DefinedTerm} it resolves to.
     */
    public function testHasDeliveryRouteAcceptsAReferenceOrATerm(): void
    {
        $delivery = new ParcelDelivery();

        $delivery->hasDeliveryRoute = '01D' ;
        $this->assertSame( '01D' , $delivery->hasDeliveryRoute );

        $term = new DefinedTerm();
        $term->name = 'West coast, midweek' ;

        $delivery->hasDeliveryRoute = $term ;
        $this->assertInstanceOf( DefinedTerm::class , $delivery->hasDeliveryRoute );
        $this->assertSame( $term , $delivery->hasDeliveryRoute );
    }

    /**
     * `#[HydrateWith]` pins the two candidates of the `Organization|Person` union,
     * which reflection alone would always resolve to its first class member. The
     * discriminator is what picks between them.
     *
     * @throws ReflectionException
     */
    public function testProviderResolvesFromItsDiscriminator(): void
    {
        $reflection = new Reflection();

        $person = $reflection->hydrate
        (
            [ Schema::PROVIDER => [ Schema::AT_TYPE => 'Person' , Schema::NAME => 'Ada Lovelace' ] ] ,
            ParcelDelivery::class
        );

        $organization = $reflection->hydrate
        (
            [ Schema::PROVIDER => [ Schema::AT_TYPE => 'Organization' , Schema::NAME => 'Etchea' ] ] ,
            ParcelDelivery::class
        );

        $this->assertInstanceOf( Person::class       , $person->provider       );
        $this->assertInstanceOf( Organization::class , $organization->provider );

        $this->assertSame( 'Ada Lovelace' , $person->provider->name       );
        $this->assertSame( 'Etchea'       , $organization->provider->name );
    }

    /**
     * Without a discriminator the attribute cannot decide, and the fallback is the
     * first candidate — `Organization`, the safe default. Documented rather than
     * fixed : a caller needing more reaches for `hydrateOrganizationOrPerson()`.
     *
     * @throws ReflectionException
     */
    public function testProviderFallsBackToOrganizationWithoutADiscriminator(): void
    {
        $delivery = ( new Reflection() )->hydrate
        (
            [ Schema::PROVIDER => [ 'givenName' => 'Ada' , 'familyName' => 'Lovelace' ] ] ,
            ParcelDelivery::class
        );

        $this->assertInstanceOf( Organization::class , $delivery->provider );
    }

    public function testPropertyNameConstants(): void
    {
        $this->assertSame( 'deliveryAddress'       , Schema::DELIVERY_ADDRESS        );
        $this->assertSame( 'deliveryStatus'        , Schema::DELIVERY_STATUS         );
        $this->assertSame( 'expectedArrivalFrom'   , Schema::EXPECTED_ARRIVAL_FROM   );
        $this->assertSame( 'expectedArrivalUntil'  , Schema::EXPECTED_ARRIVAL_UNTIL  );
        $this->assertSame( 'hasDeliveryMethod'     , Schema::HAS_DELIVERY_METHOD     );
        $this->assertSame( 'hasDeliveryRoute'      , Schema::HAS_DELIVERY_ROUTE      );
        $this->assertSame( 'itemShipped'           , Schema::ITEM_SHIPPED            );
        $this->assertSame( 'originAddress'         , Schema::ORIGIN_ADDRESS          );
        $this->assertSame( 'partOfOrder'           , Schema::PART_OF_ORDER           );
        $this->assertSame( 'provider'              , Schema::PROVIDER                );
        $this->assertSame( 'requestedDeliveryDate' , Schema::REQUESTED_DELIVERY_DATE );
        $this->assertSame( 'trackingNumber'        , Schema::TRACKING_NUMBER         );
        $this->assertSame( 'trackingUrl'           , Schema::TRACKING_URL            );
    }

    /**
     * Every constant of the trait names a property that really exists on the class.
     */
    public function testEveryConstantNamesAnExistingProperty(): void
    {
        $names =
        [
            Schema::DELIVERY_ADDRESS        ,
            Schema::DELIVERY_STATUS         ,
            Schema::EXPECTED_ARRIVAL_FROM   ,
            Schema::EXPECTED_ARRIVAL_UNTIL  ,
            Schema::HAS_DELIVERY_METHOD     ,
            Schema::HAS_DELIVERY_ROUTE      ,
            Schema::ITEM_SHIPPED            ,
            Schema::ORIGIN_ADDRESS          ,
            Schema::PART_OF_ORDER           ,
            Schema::PROVIDER                ,
            Schema::REQUESTED_DELIVERY_DATE ,
            Schema::TRACKING_NUMBER         ,
            Schema::TRACKING_URL            ,
        ];

        foreach ( $names as $name )
        {
            $this->assertTrue
            (
                property_exists( ParcelDelivery::class , $name ) ,
                sprintf( 'ParcelDelivery has no "%s" property.' , $name )
            );
        }
    }
}
