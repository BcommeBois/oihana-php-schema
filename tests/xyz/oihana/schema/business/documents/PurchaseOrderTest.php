<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\PurchaseOrder;
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
