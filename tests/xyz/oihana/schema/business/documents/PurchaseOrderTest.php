<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\PurchaseOrder;
use xyz\oihana\schema\business\documents\Quote;
use xyz\oihana\schema\constants\Oihana;

class PurchaseOrderTest extends TestCase
{
    public function testIsBusinessDocument(): void
    {
        $this->assertInstanceOf( BusinessDocument::class , new PurchaseOrder() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , PurchaseOrder::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'referencesQuote' , PurchaseOrder::REFERENCES_QUOTE );

        $this->assertSame( Oihana::REFERENCES_QUOTE , PurchaseOrder::REFERENCES_QUOTE );
    }

    public function testDefaults(): void
    {
        $order = new PurchaseOrder() ;

        $this->assertNull( $order->referencesQuote ?? null );
    }

    /**
     * A single associative array hydrates into one {@see Quote}.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesReferencesQuote(): void
    {
        $order = new Reflection()->hydrate
        (
            [
                PurchaseOrder::REFERENCES_QUOTE => [ BusinessDocument::CURRENCY => 'EUR' ] ,
            ],
            PurchaseOrder::class
        );

        $this->assertInstanceOf( Quote::class , $order->referencesQuote ) ;
        $this->assertSame( 'EUR' , $order->referencesQuote->currency ) ;
    }

    /**
     * A list of arrays hydrates into an array of {@see Quote} — one or more
     * accepted quotes aggregated into a single purchase order.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesReferencesQuoteList(): void
    {
        $order = new Reflection()->hydrate
        (
            [
                PurchaseOrder::REFERENCES_QUOTE =>
                [
                    [ BusinessDocument::CURRENCY => 'EUR' ] ,
                    [ BusinessDocument::CURRENCY => 'USD' ] ,
                ] ,
            ],
            PurchaseOrder::class
        );

        $this->assertIsArray( $order->referencesQuote ) ;
        $this->assertCount( 2 , $order->referencesQuote ) ;
        $this->assertInstanceOf( Quote::class , $order->referencesQuote[ 0 ] ) ;
        $this->assertInstanceOf( Quote::class , $order->referencesQuote[ 1 ] ) ;
        $this->assertSame( 'USD' , $order->referencesQuote[ 1 ]->currency ) ;
    }

    public function testInheritsBusinessDocumentProperties(): void
    {
        $order = new PurchaseOrder
        ([
            BusinessDocument::CURRENCY   => 'EUR' ,
            BusinessDocument::ISSUE_DATE => '2026-01-15' ,
        ]);

        $this->assertSame( 'EUR' , $order->currency ) ;
        $this->assertSame( '2026-01-15' , $order->issueDate ) ;
    }
}
