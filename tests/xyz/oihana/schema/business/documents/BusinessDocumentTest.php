<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Intangible;
use org\schema\Organization;
use org\schema\ParcelDelivery;
use org\schema\Person;
use org\schema\Place;
use org\schema\PostalAddress;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\BusinessDocumentLine;
use xyz\oihana\schema\business\documents\DocumentTotals;
use xyz\oihana\schema\business\documents\PaymentSchedule;
use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\enumerations\BusinessDocumentStatus;

class BusinessDocumentTest extends TestCase
{
    public function testIsIntangible(): void
    {
        $this->assertInstanceOf( Intangible::class , new BusinessDocument() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , BusinessDocument::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'adjustments'    , BusinessDocument::ADJUSTMENTS     );
        $this->assertSame( 'attachments'    , BusinessDocument::ATTACHMENTS     );
        $this->assertSame( 'author'         , BusinessDocument::AUTHOR          );
        $this->assertSame( 'billingAddress' , BusinessDocument::BILLING_ADDRESS );
        $this->assertSame( 'contact'        , BusinessDocument::CONTACT         );
        $this->assertSame( 'currency'       , BusinessDocument::CURRENCY        );
        $this->assertSame( 'customer'       , BusinessDocument::CUSTOMER        );
        $this->assertSame( 'direction'      , BusinessDocument::DIRECTION       );
        $this->assertSame( 'documentLines'  , BusinessDocument::DOCUMENT_LINES  );
        $this->assertSame( 'issueDate'      , BusinessDocument::ISSUE_DATE      );
        $this->assertSame( 'orderDelivery'  , BusinessDocument::ORDER_DELIVERY  );
        $this->assertSame( 'paymentTerms'   , BusinessDocument::PAYMENT_TERMS   );
        $this->assertSame( 'pointOfSale'    , BusinessDocument::POINT_OF_SALE   );
        $this->assertSame( 'references'     , BusinessDocument::REFERENCES      );
        $this->assertSame( 'seller'         , BusinessDocument::SELLER          );
        $this->assertSame( 'status'         , BusinessDocument::STATUS          );
        $this->assertSame( 'taxes'          , BusinessDocument::TAXES           );
        $this->assertSame( 'totals'         , BusinessDocument::TOTALS          );

        $this->assertSame( Oihana::ADJUSTMENTS     , BusinessDocument::ADJUSTMENTS     );
        $this->assertSame( Oihana::BILLING_ADDRESS , BusinessDocument::BILLING_ADDRESS );
        $this->assertSame( Oihana::CONTACT         , BusinessDocument::CONTACT         );
        $this->assertSame( Oihana::CUSTOMER        , BusinessDocument::CUSTOMER        );
        $this->assertSame( Oihana::ORDER_DELIVERY  , BusinessDocument::ORDER_DELIVERY  );
        $this->assertSame( Oihana::POINT_OF_SALE   , BusinessDocument::POINT_OF_SALE   );
        $this->assertSame( Oihana::TOTALS          , BusinessDocument::TOTALS          );
    }

    public function testDefaults(): void
    {
        $document = new BusinessDocument() ;

        $this->assertNull( $document->adjustments    ?? null );
        $this->assertNull( $document->attachments    ?? null );
        $this->assertNull( $document->author         ?? null );
        $this->assertNull( $document->billingAddress ?? null );
        $this->assertNull( $document->contact        ?? null );
        $this->assertNull( $document->currency       ?? null );
        $this->assertNull( $document->customer       ?? null );
        $this->assertNull( $document->direction      ?? null );
        $this->assertNull( $document->documentLines  ?? null );
        $this->assertNull( $document->issueDate      ?? null );
        $this->assertNull( $document->orderDelivery  ?? null );
        $this->assertNull( $document->paymentTerms   ?? null );
        $this->assertNull( $document->pointOfSale    ?? null );
        $this->assertNull( $document->references     ?? null );
        $this->assertNull( $document->seller         ?? null );
        $this->assertNull( $document->status         ?? null );
        $this->assertNull( $document->taxes          ?? null );
        $this->assertNull( $document->totals         ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $document = new BusinessDocument
        ([
            BusinessDocument::CURRENCY   => 'EUR' ,
            BusinessDocument::ISSUE_DATE => '2026-01-15' ,
            BusinessDocument::STATUS     => BusinessDocumentStatus::DRAFT ,
            BusinessDocument::DIRECTION  => BusinessDocumentDirection::SALE ,
            BusinessDocument::CUSTOMER   => new Person([ 'name' => 'Jane Doe' ]) ,
            BusinessDocument::SELLER     => new Organization([ 'name' => 'ACME Supplies' ]) ,
            BusinessDocument::AUTHOR     => new Organization([ 'name' => 'ACME Supplies' ]) ,
        ]);

        $this->assertSame( 'EUR' , $document->currency ) ;
        $this->assertSame( '2026-01-15' , $document->issueDate ) ;
        $this->assertSame( BusinessDocumentStatus::DRAFT , $document->status ) ;
        $this->assertSame( BusinessDocumentDirection::SALE , $document->direction ) ;
        $this->assertInstanceOf( Person::class , $document->customer ) ;
        $this->assertInstanceOf( Organization::class , $document->seller ) ;
        $this->assertInstanceOf( Organization::class , $document->author ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesNestedValueObjects(): void
    {
        $document = new Reflection()->hydrate
        (
            [
                BusinessDocument::DOCUMENT_LINES  => [ [ 'position' => 1 , 'quantity' => 2 ] ] ,
                BusinessDocument::ADJUSTMENTS     => [ [ 'type' => 'discount' , 'percentage' => 5 , 'reason' => 'Order-level discount' ] ] ,
                BusinessDocument::TAXES           => [ [ 'category' => 'VAT' , 'rate' => 20.0 ] ] ,
                BusinessDocument::TOTALS          => [ 'subtotal' => [ 'value' => 100 , 'currency' => 'EUR' ] ] ,
                BusinessDocument::PAYMENT_TERMS   => [ 'installments' => [ [ 'dueDate' => '2026-02-01' , 'percentage' => 100.0 ] ] ] ,
                BusinessDocument::BILLING_ADDRESS => [ 'streetAddress' => '1 rue du Test' , 'postalCode' => '12000' , 'addressLocality' => 'Rodez' ] ,
                BusinessDocument::CONTACT         => [ 'name' => 'Jane Doe' ] ,
                BusinessDocument::ORDER_DELIVERY  => [ 'trackingNumber' => 'PKG-1' , 'expectedArrivalFrom' => '2026-07-21' ] ,
                BusinessDocument::POINT_OF_SALE   => [ 'name' => 'Rodez' ] ,
            ],
            BusinessDocument::class
        );

        $this->assertInstanceOf( BusinessDocumentLine::class , $document->documentLines[ 0 ] ) ;
        $this->assertSame( 1 , $document->documentLines[ 0 ]->position ) ;

        $this->assertInstanceOf( PostalAddress::class , $document->billingAddress ) ;
        $this->assertSame( '1 rue du Test' , $document->billingAddress->streetAddress ) ;

        $this->assertInstanceOf( Person::class , $document->contact ) ;
        $this->assertSame( 'Jane Doe' , $document->contact->name ) ;

        $this->assertInstanceOf( ParcelDelivery::class , $document->orderDelivery ) ;
        $this->assertSame( 'PKG-1' , $document->orderDelivery->trackingNumber ) ;

        $this->assertInstanceOf( Place::class , $document->pointOfSale ) ;
        $this->assertSame( 'Rodez' , $document->pointOfSale->name ) ;

        $this->assertInstanceOf( Adjustment::class , $document->adjustments[ 0 ] ) ;
        $this->assertSame( 5 , $document->adjustments[ 0 ]->percentage ) ;
        $this->assertSame( 'Order-level discount' , $document->adjustments[ 0 ]->reason ) ;

        $this->assertInstanceOf( TaxDetail::class , $document->taxes[ 0 ] ) ;

        $this->assertInstanceOf( DocumentTotals::class , $document->totals ) ;
        $this->assertInstanceOf( \org\schema\MonetaryAmount::class , $document->totals->subtotal ) ;

        $this->assertInstanceOf( PaymentSchedule::class , $document->paymentTerms ) ;
        $this->assertCount( 1 , $document->paymentTerms->installments ) ;
    }

    public function testConstructorAcceptsDocumentLevelAdjustments(): void
    {
        $adjustment = new Adjustment([ Adjustment::TYPE => 'shipping' , Adjustment::REASON => 'Carriage' ]) ;

        $document = new BusinessDocument([ BusinessDocument::ADJUSTMENTS => [ $adjustment ] ]) ;

        $this->assertIsArray( $document->adjustments ) ;
        $this->assertInstanceOf( Adjustment::class , $document->adjustments[ 0 ] ) ;
        $this->assertSame( 'Carriage' , $document->adjustments[ 0 ]->reason ) ;
    }

    public function testPaymentTermsAcceptsFreeText(): void
    {
        $document = new BusinessDocument([ BusinessDocument::PAYMENT_TERMS => 'Net 30 days' ]) ;

        $this->assertSame( 'Net 30 days' , $document->paymentTerms ) ;
    }
}
