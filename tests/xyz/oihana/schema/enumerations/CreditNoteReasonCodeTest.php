<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\CreditNoteReasonCode;

class CreditNoteReasonCodeTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new CreditNoteReasonCode() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/CreditNoteReasonCode#DuplicateBilling'   , CreditNoteReasonCode::DUPLICATE_BILLING    );
        $this->assertSame( 'https://schema.oihana.xyz/CreditNoteReasonCode#GoodsReturned'      , CreditNoteReasonCode::GOODS_RETURNED       );
        $this->assertSame( 'https://schema.oihana.xyz/CreditNoteReasonCode#Goodwill'           , CreditNoteReasonCode::GOODWILL             );
        $this->assertSame( 'https://schema.oihana.xyz/CreditNoteReasonCode#Other'              , CreditNoteReasonCode::OTHER                );
        $this->assertSame( 'https://schema.oihana.xyz/CreditNoteReasonCode#PricingError'       , CreditNoteReasonCode::PRICING_ERROR        );
        $this->assertSame( 'https://schema.oihana.xyz/CreditNoteReasonCode#ServiceNotRendered' , CreditNoteReasonCode::SERVICE_NOT_RENDERED );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( CreditNoteReasonCode::includes( CreditNoteReasonCode::GOODS_RETURNED ) );
        $this->assertFalse( CreditNoteReasonCode::includes( 'unknown' ) );
    }
}
