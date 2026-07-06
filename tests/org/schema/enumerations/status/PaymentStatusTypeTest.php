<?php

namespace tests\org\schema\enumerations\status ;

use PHPUnit\Framework\TestCase;

use org\schema\enumerations\status\PaymentStatusType;
use org\schema\enumerations\StatusEnumeration;

class PaymentStatusTypeTest extends TestCase
{
    public function testIsStatusEnumeration(): void
    {
        $this->assertInstanceOf( StatusEnumeration::class , new PaymentStatusType() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.org/PaymentAutomaticallyApplied' , PaymentStatusType::PAYMENT_AUTOMATICALLY_APPLIED );
        $this->assertSame( 'https://schema.org/PaymentComplete'             , PaymentStatusType::PAYMENT_COMPLETE               );
        $this->assertSame( 'https://schema.org/PaymentDeclined'             , PaymentStatusType::PAYMENT_DECLINED               );
        $this->assertSame( 'https://schema.org/PaymentDue'                 , PaymentStatusType::PAYMENT_DUE                    );
        $this->assertSame( 'https://schema.org/PaymentPastDue'             , PaymentStatusType::PAYMENT_PAST_DUE               );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( PaymentStatusType::includes( PaymentStatusType::PAYMENT_DUE ) );
        $this->assertFalse( PaymentStatusType::includes( 'unknown' ) );
    }
}
