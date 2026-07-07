<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Intangible;
use org\schema\Organization;
use org\schema\Person;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\BusinessDocumentLine;
use xyz\oihana\schema\business\documents\DocumentTotals;
use xyz\oihana\schema\business\documents\PaymentSchedule;
use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\constants\Oihana;
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
        $this->assertSame( 'adjustments'    , BusinessDocument::ADJUSTMENTS    );
        $this->assertSame( 'attachments'    , BusinessDocument::ATTACHMENTS    );
        $this->assertSame( 'currency'       , BusinessDocument::CURRENCY       );
        $this->assertSame( 'customer'       , BusinessDocument::CUSTOMER       );
        $this->assertSame( 'documentLines'  , BusinessDocument::DOCUMENT_LINES );
        $this->assertSame( 'issueDate'      , BusinessDocument::ISSUE_DATE     );
        $this->assertSame( 'paymentTerms'   , BusinessDocument::PAYMENT_TERMS  );
        $this->assertSame( 'references'     , BusinessDocument::REFERENCES     );
        $this->assertSame( 'seller'         , BusinessDocument::SELLER         );
        $this->assertSame( 'status'         , BusinessDocument::STATUS         );
        $this->assertSame( 'taxes'          , BusinessDocument::TAXES          );
        $this->assertSame( 'totals'         , BusinessDocument::TOTALS         );

        $this->assertSame( Oihana::ADJUSTMENTS , BusinessDocument::ADJUSTMENTS );
        $this->assertSame( Oihana::CUSTOMER    , BusinessDocument::CUSTOMER    );
        $this->assertSame( Oihana::TOTALS      , BusinessDocument::TOTALS      );
    }

    public function testDefaults(): void
    {
        $document = new BusinessDocument() ;

        $this->assertNull( $document->adjustments   ?? null );
        $this->assertNull( $document->attachments   ?? null );
        $this->assertNull( $document->currency      ?? null );
        $this->assertNull( $document->customer      ?? null );
        $this->assertNull( $document->documentLines ?? null );
        $this->assertNull( $document->issueDate     ?? null );
        $this->assertNull( $document->paymentTerms  ?? null );
        $this->assertNull( $document->references    ?? null );
        $this->assertNull( $document->seller        ?? null );
        $this->assertNull( $document->status        ?? null );
        $this->assertNull( $document->taxes         ?? null );
        $this->assertNull( $document->totals        ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $document = new BusinessDocument
        ([
            BusinessDocument::CURRENCY   => 'EUR' ,
            BusinessDocument::ISSUE_DATE => '2026-01-15' ,
            BusinessDocument::STATUS     => BusinessDocumentStatus::DRAFT ,
            BusinessDocument::CUSTOMER   => new Person([ 'name' => 'Jane Doe' ]) ,
            BusinessDocument::SELLER     => new Organization([ 'name' => 'ACME Supplies' ]) ,
        ]);

        $this->assertSame( 'EUR' , $document->currency ) ;
        $this->assertSame( '2026-01-15' , $document->issueDate ) ;
        $this->assertSame( BusinessDocumentStatus::DRAFT , $document->status ) ;
        $this->assertInstanceOf( Person::class , $document->customer ) ;
        $this->assertInstanceOf( Organization::class , $document->seller ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesNestedValueObjects(): void
    {
        $document = new Reflection()->hydrate
        (
            [
                BusinessDocument::DOCUMENT_LINES => [ [ 'position' => 1 , 'quantity' => 2 ] ] ,
                BusinessDocument::ADJUSTMENTS    => [ [ 'type' => 'discount' , 'percentage' => 5 , 'reason' => 'Order-level discount' ] ] ,
                BusinessDocument::TAXES          => [ [ 'category' => 'VAT' , 'rate' => 20.0 ] ] ,
                BusinessDocument::TOTALS         => [ 'subtotal' => [ 'value' => 100 , 'currency' => 'EUR' ] ] ,
                BusinessDocument::PAYMENT_TERMS  => [ 'installments' => [ [ 'dueDate' => '2026-02-01' , 'percentage' => 100.0 ] ] ] ,
            ],
            BusinessDocument::class
        );

        $this->assertInstanceOf( BusinessDocumentLine::class , $document->documentLines[ 0 ] ) ;
        $this->assertSame( 1 , $document->documentLines[ 0 ]->position ) ;

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
