<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\CategoryCode;
use org\schema\Organization;
use org\schema\Person;
use org\schema\enumerations\status\PaymentComplete;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\business\documents\PurchaseOrder;
use xyz\oihana\schema\constants\Oihana;

class InvoiceTest extends TestCase
{
    public function testIsBusinessDocument(): void
    {
        $this->assertInstanceOf( BusinessDocument::class , new Invoice() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , Invoice::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'accountId'            , Invoice::ACCOUNT_ID             );
        $this->assertSame( 'billingPeriod'        , Invoice::BILLING_PERIOD         );
        $this->assertSame( 'broker'               , Invoice::BROKER                 );
        $this->assertSame( 'category'             , Invoice::CATEGORY               );
        $this->assertSame( 'confirmationNumber'   , Invoice::CONFIRMATION_NUMBER    );
        $this->assertSame( 'paymentDueDate'       , Invoice::PAYMENT_DUE_DATE       );
        $this->assertSame( 'paymentStatus'        , Invoice::PAYMENT_STATUS         );
        $this->assertSame( 'provider'             , Invoice::PROVIDER               );
        $this->assertSame( 'referencesOrder'      , Invoice::REFERENCES_ORDER       );
        $this->assertSame( 'scheduledPaymentDate' , Invoice::SCHEDULED_PAYMENT_DATE );

        $this->assertSame( Oihana::ACCOUNT_ID , Invoice::ACCOUNT_ID );
        $this->assertSame( Oihana::PROVIDER   , Invoice::PROVIDER   );
    }

    public function testDefaults(): void
    {
        $invoice = new Invoice() ;

        $this->assertNull( $invoice->accountId            ?? null );
        $this->assertNull( $invoice->billingPeriod        ?? null );
        $this->assertNull( $invoice->broker               ?? null );
        $this->assertNull( $invoice->category             ?? null );
        $this->assertNull( $invoice->confirmationNumber   ?? null );
        $this->assertNull( $invoice->paymentDueDate       ?? null );
        $this->assertNull( $invoice->paymentStatus        ?? null );
        $this->assertNull( $invoice->provider             ?? null );
        $this->assertNull( $invoice->referencesOrder      ?? null );
        $this->assertNull( $invoice->scheduledPaymentDate ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $invoice = new Invoice
        ([
            Invoice::ACCOUNT_ID            => 'ACC-001' ,
            Invoice::CONFIRMATION_NUMBER   => 'CONF-42' ,
            Invoice::PAYMENT_DUE_DATE      => '2026-02-15' ,
            Invoice::SCHEDULED_PAYMENT_DATE => '2026-02-10' ,
            Invoice::BROKER                => new Organization([ 'name' => 'Broker Inc.' ]) ,
            Invoice::PROVIDER              => new Person([ 'name' => 'Jane Doe' ]) ,
        ]);

        $this->assertSame( 'ACC-001' , $invoice->accountId ) ;
        $this->assertSame( 'CONF-42' , $invoice->confirmationNumber ) ;
        $this->assertSame( '2026-02-15' , $invoice->paymentDueDate ) ;
        $this->assertSame( '2026-02-10' , $invoice->scheduledPaymentDate ) ;
        $this->assertInstanceOf( Organization::class , $invoice->broker ) ;
        $this->assertInstanceOf( Person::class , $invoice->provider ) ;
    }

    public function testPaymentStatusAcceptsPaymentStatusTypeMemberClassName(): void
    {
        $invoice = new Invoice([ Invoice::PAYMENT_STATUS => PaymentComplete::class ]) ;

        $this->assertSame( PaymentComplete::class , $invoice->paymentStatus ) ;
    }

    public function testCategoryAcceptsPlainStringOrCategoryCode(): void
    {
        $invoice = new Invoice([ Invoice::CATEGORY => 'consulting' ]) ;
        $this->assertSame( 'consulting' , $invoice->category ) ;

        $invoice = new Invoice([ Invoice::CATEGORY => new CategoryCode([ 'name' => 'Consulting' ]) ]) ;
        $this->assertInstanceOf( CategoryCode::class , $invoice->category ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesReferencesOrder(): void
    {
        $invoice = new Reflection()->hydrate
        (
            [
                Invoice::REFERENCES_ORDER => [ BusinessDocument::CURRENCY => 'EUR' ] ,
            ],
            Invoice::class
        );

        $this->assertInstanceOf( PurchaseOrder::class , $invoice->referencesOrder ) ;
        $this->assertSame( 'EUR' , $invoice->referencesOrder->currency ) ;
    }

    /**
     * A list of arrays hydrates into an array of {@see PurchaseOrder} — one or
     * more purchase orders combined into a single invoice.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesReferencesOrderList(): void
    {
        $invoice = new Reflection()->hydrate
        (
            [
                Invoice::REFERENCES_ORDER =>
                [
                    [ BusinessDocument::CURRENCY => 'EUR' ] ,
                    [ BusinessDocument::CURRENCY => 'USD' ] ,
                ] ,
            ],
            Invoice::class
        );

        $this->assertIsArray( $invoice->referencesOrder ) ;
        $this->assertCount( 2 , $invoice->referencesOrder ) ;
        $this->assertInstanceOf( PurchaseOrder::class , $invoice->referencesOrder[ 0 ] ) ;
        $this->assertInstanceOf( PurchaseOrder::class , $invoice->referencesOrder[ 1 ] ) ;
        $this->assertSame( 'USD' , $invoice->referencesOrder[ 1 ]->currency ) ;
    }

    public function testInheritsBusinessDocumentProperties(): void
    {
        $invoice = new Invoice
        ([
            BusinessDocument::CURRENCY => 'EUR' ,
            Invoice::ACCOUNT_ID        => 'ACC-001' ,
        ]);

        $this->assertSame( 'EUR' , $invoice->currency ) ;
        $this->assertSame( 'ACC-001' , $invoice->accountId ) ;
    }
}
