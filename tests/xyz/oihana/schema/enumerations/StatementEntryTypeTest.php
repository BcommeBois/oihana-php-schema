<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\StatementEntryType;

class StatementEntryTypeTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new StatementEntryType() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/StatementEntryType#Adjustment'     , StatementEntryType::ADJUSTMENT      );
        $this->assertSame( 'https://schema.oihana.xyz/StatementEntryType#CreditNote'     , StatementEntryType::CREDIT_NOTE     );
        $this->assertSame( 'https://schema.oihana.xyz/StatementEntryType#Invoice'        , StatementEntryType::INVOICE         );
        $this->assertSame( 'https://schema.oihana.xyz/StatementEntryType#OpeningBalance' , StatementEntryType::OPENING_BALANCE );
        $this->assertSame( 'https://schema.oihana.xyz/StatementEntryType#Other'          , StatementEntryType::OTHER           );
        $this->assertSame( 'https://schema.oihana.xyz/StatementEntryType#Payment'        , StatementEntryType::PAYMENT         );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( StatementEntryType::includes( StatementEntryType::INVOICE ) );
        $this->assertFalse( StatementEntryType::includes( 'unknown' ) );
    }
}
