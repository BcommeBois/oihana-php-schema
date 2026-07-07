<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\BusinessDocumentLine;
use xyz\oihana\schema\business\documents\DocumentTotals;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\business\documents\Receipt;
use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\constants\Oihana;

class ReceiptTest extends TestCase
{
    public function testIsBusinessDocument(): void
    {
        $this->assertInstanceOf( BusinessDocument::class , new Receipt() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , Receipt::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'confirmationNumber' , Receipt::CONFIRMATION_NUMBER );
        $this->assertSame( 'paymentMethod'       , Receipt::PAYMENT_METHOD      );
        $this->assertSame( 'paymentMethodId'     , Receipt::PAYMENT_METHOD_ID   );
        $this->assertSame( 'referencesInvoice'   , Receipt::REFERENCES_INVOICE  );

        $this->assertSame( Oihana::PAYMENT_METHOD , Receipt::PAYMENT_METHOD );
    }

    public function testDefaults(): void
    {
        $receipt = new Receipt() ;

        $this->assertNull( $receipt->confirmationNumber ?? null );
        $this->assertNull( $receipt->paymentMethod       ?? null );
        $this->assertNull( $receipt->paymentMethodId     ?? null );
        $this->assertNull( $receipt->referencesInvoice   ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $receipt = new Receipt
        ([
            Receipt::CONFIRMATION_NUMBER => 'CONF-42' ,
            Receipt::PAYMENT_METHOD      => 'CreditCard' ,
            Receipt::PAYMENT_METHOD_ID   => '4242' ,
        ]);

        $this->assertSame( 'CONF-42' , $receipt->confirmationNumber ) ;
        $this->assertSame( 'CreditCard' , $receipt->paymentMethod ) ;
        $this->assertSame( '4242' , $receipt->paymentMethodId ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesReferencesInvoice(): void
    {
        $receipt = new Reflection()->hydrate
        (
            [
                Receipt::REFERENCES_INVOICE => [ BusinessDocument::CURRENCY => 'EUR' ] ,
            ],
            Receipt::class
        );

        $this->assertInstanceOf( Invoice::class , $receipt->referencesInvoice ) ;
        $this->assertSame( 'EUR' , $receipt->referencesInvoice->currency ) ;
    }

    /**
     * A list of arrays hydrates into an array of {@see Invoice} — a single
     * payment settling more than one invoice at once.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesReferencesInvoiceList(): void
    {
        $receipt = new Reflection()->hydrate
        (
            [
                Receipt::REFERENCES_INVOICE =>
                [
                    [ BusinessDocument::CURRENCY => 'EUR' ] ,
                    [ BusinessDocument::CURRENCY => 'USD' ] ,
                ] ,
            ],
            Receipt::class
        );

        $this->assertIsArray( $receipt->referencesInvoice ) ;
        $this->assertCount( 2 , $receipt->referencesInvoice ) ;
        $this->assertInstanceOf( Invoice::class , $receipt->referencesInvoice[ 0 ] ) ;
        $this->assertInstanceOf( Invoice::class , $receipt->referencesInvoice[ 1 ] ) ;
        $this->assertSame( 'USD' , $receipt->referencesInvoice[ 1 ]->currency ) ;
    }

    public function testInheritsBusinessDocumentProperties(): void
    {
        $receipt = new Receipt([ BusinessDocument::CURRENCY => 'EUR' ]) ;

        $this->assertSame( 'EUR' , $receipt->currency ) ;
    }

    /**
     * A direct/cash sale with no prior invoice : `referencesInvoice` stays
     * null and the sale is carried on the inherited `documentLines`/`taxes`/
     * `totals`, exactly as any other business document.
     *
     * @throws ReflectionException
     */
    public function testDirectSaleWithoutInvoice(): void
    {
        $receipt = new Reflection()->hydrate
        (
            [
                Receipt::CONFIRMATION_NUMBER => 'POS-2026-001' ,
                Receipt::PAYMENT_METHOD      => 'Cash' ,
                BusinessDocument::DOCUMENT_LINES => [ [ 'position' => 1 , 'quantity' => 2 ] ] ,
                BusinessDocument::TAXES          => [ [ 'category' => 'VAT' , 'rate' => 20.0 ] ] ,
                BusinessDocument::TOTALS         => [ 'total' => [ 'value' => 24 , 'currency' => 'EUR' ] ] ,
            ],
            Receipt::class
        );

        $this->assertNull( $receipt->referencesInvoice ?? null ) ;
        $this->assertInstanceOf( BusinessDocumentLine::class , $receipt->documentLines[ 0 ] ) ;
        $this->assertInstanceOf( TaxDetail::class , $receipt->taxes[ 0 ] ) ;
        $this->assertInstanceOf( DocumentTotals::class , $receipt->totals ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $receipt->totals->total ) ;
        $this->assertSame( 'POS-2026-001' , $receipt->confirmationNumber ) ;
    }
}
