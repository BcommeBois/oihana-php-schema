<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\CreditNoteDisposition;

class CreditNoteDispositionTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new CreditNoteDisposition() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/CreditNoteDisposition#Pending'   , CreditNoteDisposition::PENDING   );
        $this->assertSame( 'https://schema.oihana.xyz/CreditNoteDisposition#Reapplied' , CreditNoteDisposition::REAPPLIED );
        $this->assertSame( 'https://schema.oihana.xyz/CreditNoteDisposition#Refunded'  , CreditNoteDisposition::REFUNDED  );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( CreditNoteDisposition::includes( CreditNoteDisposition::REFUNDED ) );
        $this->assertFalse( CreditNoteDisposition::includes( 'unknown' ) );
    }
}
