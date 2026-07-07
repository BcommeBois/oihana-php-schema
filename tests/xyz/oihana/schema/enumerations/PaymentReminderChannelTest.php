<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\PaymentReminderChannel;

class PaymentReminderChannelTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new PaymentReminderChannel() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/PaymentReminderChannel#Email'  , PaymentReminderChannel::EMAIL  );
        $this->assertSame( 'https://schema.oihana.xyz/PaymentReminderChannel#Other'  , PaymentReminderChannel::OTHER  );
        $this->assertSame( 'https://schema.oihana.xyz/PaymentReminderChannel#Phone'  , PaymentReminderChannel::PHONE  );
        $this->assertSame( 'https://schema.oihana.xyz/PaymentReminderChannel#Postal' , PaymentReminderChannel::POSTAL );
        $this->assertSame( 'https://schema.oihana.xyz/PaymentReminderChannel#Sms'    , PaymentReminderChannel::SMS    );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( PaymentReminderChannel::includes( PaymentReminderChannel::EMAIL ) );
        $this->assertFalse( PaymentReminderChannel::includes( 'unknown' ) );
    }
}
