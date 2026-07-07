<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\PaymentReminderLevel;

class PaymentReminderLevelTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new PaymentReminderLevel() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/PaymentReminderLevel#FinalNotice'    , PaymentReminderLevel::FINAL_NOTICE    );
        $this->assertSame( 'https://schema.oihana.xyz/PaymentReminderLevel#FirstReminder'  , PaymentReminderLevel::FIRST_REMINDER  );
        $this->assertSame( 'https://schema.oihana.xyz/PaymentReminderLevel#FormalNotice'   , PaymentReminderLevel::FORMAL_NOTICE   );
        $this->assertSame( 'https://schema.oihana.xyz/PaymentReminderLevel#Reminder'       , PaymentReminderLevel::REMINDER        );
        $this->assertSame( 'https://schema.oihana.xyz/PaymentReminderLevel#SecondReminder' , PaymentReminderLevel::SECOND_REMINDER );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( PaymentReminderLevel::includes( PaymentReminderLevel::REMINDER ) );
        $this->assertFalse( PaymentReminderLevel::includes( 'unknown' ) );
    }
}
