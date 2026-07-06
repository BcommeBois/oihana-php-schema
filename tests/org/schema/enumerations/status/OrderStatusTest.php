<?php

namespace tests\org\schema\enumerations\status ;

use PHPUnit\Framework\TestCase;

use org\schema\enumerations\status\OrderCancelled;
use org\schema\enumerations\status\OrderDelivered;
use org\schema\enumerations\status\OrderInTransit;
use org\schema\enumerations\status\OrderPaymentDue;
use org\schema\enumerations\status\OrderPickupAvailable;
use org\schema\enumerations\status\OrderProblem;
use org\schema\enumerations\status\OrderProcessing;
use org\schema\enumerations\status\OrderReturned;
use org\schema\enumerations\status\OrderStatus;
use org\schema\enumerations\StatusEnumeration;

class OrderStatusTest extends TestCase
{
    public function testIsStatusEnumeration(): void
    {
        $this->assertInstanceOf( StatusEnumeration::class , new OrderStatus() );
    }

    public function testMembersExtendOrderStatus(): void
    {
        $this->assertInstanceOf( OrderStatus::class , new OrderCancelled()        );
        $this->assertInstanceOf( OrderStatus::class , new OrderDelivered()        );
        $this->assertInstanceOf( OrderStatus::class , new OrderInTransit()        );
        $this->assertInstanceOf( OrderStatus::class , new OrderPaymentDue()       );
        $this->assertInstanceOf( OrderStatus::class , new OrderPickupAvailable()  );
        $this->assertInstanceOf( OrderStatus::class , new OrderProblem()          );
        $this->assertInstanceOf( OrderStatus::class , new OrderProcessing()       );
        $this->assertInstanceOf( OrderStatus::class , new OrderReturned()         );
    }
}
