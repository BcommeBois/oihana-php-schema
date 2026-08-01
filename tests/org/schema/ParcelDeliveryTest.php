<?php

namespace tests\org\schema ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\constants\Schema;
use org\schema\DefinedTerm;
use org\schema\enumerations\DeliveryMethod;
use org\schema\Intangible;
use org\schema\ParcelDelivery;
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

    public function testPropertyNameConstants(): void
    {
        $this->assertSame( 'deliveryAddress'       , Schema::DELIVERY_ADDRESS        );
        $this->assertSame( 'deliveryStatus'        , Schema::DELIVERY_STATUS         );
        $this->assertSame( 'expectedArrivalFrom'   , Schema::EXPECTED_ARRIVAL_FROM   );
        $this->assertSame( 'expectedArrivalUntil'  , Schema::EXPECTED_ARRIVAL_UNTIL  );
        $this->assertSame( 'hasDeliveryMethod'     , Schema::HAS_DELIVERY_METHOD     );
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
