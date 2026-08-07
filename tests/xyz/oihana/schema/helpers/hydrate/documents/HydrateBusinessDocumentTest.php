<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\Organization;
use org\schema\ParcelDelivery;
use org\schema\Person;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\BusinessDocumentLine;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\business\documents\Quote;
use xyz\oihana\schema\business\documents\TaxDetail;

use function xyz\oihana\schema\helpers\hydrate\documents\hydrateBusinessDocument;

final class HydrateBusinessDocumentTest extends TestCase
{
    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesCustomerSellerAndAuthorFromTheirType(): void
    {
        $document = hydrateBusinessDocument
        ([
            'customer' => [ '@type' => 'Person'       , 'name' => 'Jean Dupont' ] ,
            'seller'   => [ '@type' => 'Organization' , 'name' => 'ACME'        ] ,
            'author'   => [ 'name' => 'Sans type' ] ,
        ]) ;

        $this->assertInstanceOf( BusinessDocument::class , $document ) ;

        $this->assertInstanceOf( Person::class , $document->customer ) ;
        $this->assertSame( 'Jean Dupont' , $document->customer->name ) ;

        $this->assertInstanceOf( Organization::class , $document->seller ) ;
        $this->assertSame( 'ACME' , $document->seller->name ) ;

        // No @type given : falls back to Organization, the safe default.
        $this->assertInstanceOf( Organization::class , $document->author ) ;
    }

    /**
     * The carrier carries the same `Organization|Person` union, one level down
     * inside the delivery. Reflection builds the delivery through `#[HydrateAs]`,
     * so nothing inside it was re-resolved before.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testResolvesTheCarrierInsideOrderDelivery(): void
    {
        $document = hydrateBusinessDocument
        ([
            'orderDelivery' =>
            [
                'trackingNumber' => 'T-1' ,
                'provider'       => [ '@type' => 'Person' , 'name' => 'Ada Lovelace' ] ,
            ] ,
        ]) ;

        $this->assertInstanceOf( ParcelDelivery::class , $document->orderDelivery ) ;
        $this->assertInstanceOf( Person::class , $document->orderDelivery->provider ) ;
        $this->assertSame( 'Ada Lovelace' , $document->orderDelivery->provider->name ) ;

        // The rest of the delivery survives the second pass untouched.
        $this->assertSame( 'T-1' , $document->orderDelivery->trackingNumber ) ;
    }

    /**
     * An organization carrier, and one with no discriminator at all — which stays
     * an `Organization`, the safe default.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testResolvesAnOrganizationCarrierAndDefaultsWithoutADiscriminator(): void
    {
        $typed = hydrateBusinessDocument
        ([
            'orderDelivery' => [ 'provider' => [ '@type' => 'Organization' , 'name' => 'Etchea' ] ] ,
        ]) ;

        $untyped = hydrateBusinessDocument
        ([
            'orderDelivery' => [ 'provider' => [ 'name' => 'Etchea' ] ] ,
        ]) ;

        $this->assertInstanceOf( Organization::class , $typed->orderDelivery->provider   ) ;
        $this->assertInstanceOf( Organization::class , $untyped->orderDelivery->provider ) ;
    }

    /**
     * A document with no delivery, or a delivery with no carrier, goes through
     * the second pass without tripping on an uninitialized property.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testToleratesAMissingDeliveryOrCarrier(): void
    {
        $withoutDelivery = hydrateBusinessDocument( [ 'issueDate' => '2026-08-01' ] ) ;
        $withoutCarrier  = hydrateBusinessDocument( [ 'orderDelivery' => [ 'trackingNumber' => 'T-2' ] ] ) ;

        $this->assertInstanceOf( BusinessDocument::class , $withoutDelivery ) ;
        $this->assertSame( 'T-2' , $withoutCarrier->orderDelivery->trackingNumber ) ;
        $this->assertNull( $withoutCarrier->orderDelivery->provider ?? null ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testStillHydratesEveryOtherAttributeDrivenProperty(): void
    {
        $document = hydrateBusinessDocument
        ([
            'currency'      => 'EUR' ,
            'documentLines' => [ [ 'position' => 1 , 'quantity' => 5 ] ] ,
            'taxes'         => [ [ 'category' => 'VAT' , 'rate' => 20.0 ] ] ,
            'adjustments'   => [ [ 'type' => 'discount' , 'percentage' => 10.0 ] ] ,
        ]) ;

        $this->assertSame( 'EUR' , $document->currency ) ;

        $this->assertContainsOnlyInstancesOf( BusinessDocumentLine::class , $document->documentLines ) ;
        $this->assertSame( 1 , $document->documentLines[ 0 ]->position ) ;

        $this->assertContainsOnlyInstancesOf( TaxDetail::class , $document->taxes ) ;
        $this->assertContainsOnlyInstancesOf( Adjustment::class , $document->adjustments ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnInvoiceAndResolvesBrokerAndProvider(): void
    {
        $invoice = hydrateBusinessDocument
        (
            [
                'customer' => [ '@type' => 'Person' , 'name' => 'Jean Dupont' ] ,
                'broker'   => [ '@type' => 'Person' , 'name' => 'Courtier' ] ,
                'provider' => [ '@type' => 'Corporation' , 'name' => 'Sous-traitant SA' ] ,
            ],
            Invoice::class
        ) ;

        $this->assertInstanceOf( Invoice::class , $invoice ) ;

        $this->assertInstanceOf( Person::class , $invoice->customer ) ;
        $this->assertInstanceOf( Person::class , $invoice->broker   ) ;
        $this->assertSame( 'Courtier' , $invoice->broker->name ) ;

        $this->assertInstanceOf( Organization::class , $invoice->provider ) ;
        $this->assertSame( 'Sous-traitant SA' , $invoice->provider->name ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnySubclassGenerically(): void
    {
        $quote = hydrateBusinessDocument( [ 'customer' => [ '@type' => 'Person' , 'name' => 'Jean Dupont' ] ] , Quote::class ) ;

        $this->assertInstanceOf( Quote::class , $quote ) ;
        $this->assertInstanceOf( Person::class , $quote->customer ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAMinimalDocumentUntouched(): void
    {
        $document = hydrateBusinessDocument( [ 'currency' => 'EUR' ] ) ;

        $this->assertInstanceOf( BusinessDocument::class , $document ) ;
        $this->assertSame( 'EUR' , $document->currency ) ;
        $this->assertNull( $document->customer ?? null ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $documents = hydrateBusinessDocument
        ([
            [ 'currency' => 'EUR' , 'customer' => [ '@type' => 'Person' , 'name' => 'A' ] ] ,
            [ 'currency' => 'USD' , 'seller'   => [ '@type' => 'Person' , 'name' => 'B' ] ] ,
        ]) ;

        $this->assertIsArray( $documents ) ;
        $this->assertCount( 2 , $documents ) ;
        $this->assertContainsOnlyInstancesOf( BusinessDocument::class , $documents ) ;

        $this->assertInstanceOf( Person::class , $documents[ 0 ]->customer ) ;
        $this->assertInstanceOf( Person::class , $documents[ 1 ]->seller   ) ;

        $this->assertNull( hydrateBusinessDocument( [ 'raw' ] ) ) ;
        $this->assertNull( hydrateBusinessDocument( [] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAlreadyHydratedDocuments(): void
    {
        $document = new BusinessDocument( [ 'currency' => 'EUR' ] ) ;

        $this->assertSame( $document , hydrateBusinessDocument( $document ) ) ;
        $this->assertSame( [ $document ] , hydrateBusinessDocument( [ $document ] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateBusinessDocument() ) ;
        $this->assertSame( 'raw' , hydrateBusinessDocument( 'raw' ) ) ;
    }

    /**
     * An empty list is not a list of things : it says "nothing here". A party the
     * payload declares empty comes out `null`, not as the empty-ish entity a blind
     * hydration would build out of nothing.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testEmptyPartiesYieldNull(): void
    {
        $document = hydrateBusinessDocument
        ([
            'currency' => 'EUR' ,
            'customer' => [] ,
            'seller'   => [] ,
            'author'   => [] ,
        ]) ;

        $this->assertInstanceOf( BusinessDocument::class , $document ) ;

        $this->assertNull( $document->customer ) ;
        $this->assertNull( $document->seller   ) ;
        $this->assertNull( $document->author   ) ;
    }

    /**
     * The carrier of the delivery follows the same rule, one level down.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testAnEmptyDeliveryProviderYieldsNull(): void
    {
        $document = hydrateBusinessDocument
        ([
            'currency'      => 'EUR' ,
            'orderDelivery' => [ 'trackingNumber' => 'AB-42' , 'provider' => [] ] ,
        ]) ;

        $this->assertInstanceOf( BusinessDocument::class , $document ) ;
        $this->assertInstanceOf( ParcelDelivery::class , $document->orderDelivery ) ;

        $this->assertNull( $document->orderDelivery->provider ) ;
        $this->assertSame( 'AB-42' , $document->orderDelivery->trackingNumber ) ;
    }
}
