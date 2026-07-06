<?php

namespace tests\org\schema\enumerations\status ;

use PHPUnit\Framework\TestCase;

use org\schema\enumerations\status\OrderStatus;
use org\schema\enumerations\StatusEnumeration;

class OrderStatusTest extends TestCase
{
    public function testIsStatusEnumeration(): void
    {
        $this->assertInstanceOf( StatusEnumeration::class , new OrderStatus() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.org/OrderCancelled'        , OrderStatus::ORDER_CANCELLED         );
        $this->assertSame( 'https://schema.org/OrderDelivered'        , OrderStatus::ORDER_DELIVERED         );
        $this->assertSame( 'https://schema.org/OrderInTransit'        , OrderStatus::ORDER_IN_TRANSIT        );
        $this->assertSame( 'https://schema.org/OrderPaymentDue'       , OrderStatus::ORDER_PAYMENT_DUE       );
        $this->assertSame( 'https://schema.org/OrderPickupAvailable'  , OrderStatus::ORDER_PICKUP_AVAILABLE  );
        $this->assertSame( 'https://schema.org/OrderProblem'          , OrderStatus::ORDER_PROBLEM           );
        $this->assertSame( 'https://schema.org/OrderProcessing'       , OrderStatus::ORDER_PROCESSING        );
        $this->assertSame( 'https://schema.org/OrderReturned'         , OrderStatus::ORDER_RETURNED          );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( OrderStatus::includes( OrderStatus::ORDER_DELIVERED ) );
        $this->assertFalse( OrderStatus::includes( 'unknown' ) );
    }
}
