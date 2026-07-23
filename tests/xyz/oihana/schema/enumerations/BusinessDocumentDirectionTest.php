<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;

class BusinessDocumentDirectionTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new BusinessDocumentDirection() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentDirection#Sale'     , BusinessDocumentDirection::SALE     );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentDirection#Purchase' , BusinessDocumentDirection::PURCHASE );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( BusinessDocumentDirection::includes( BusinessDocumentDirection::SALE     ) );
        $this->assertTrue ( BusinessDocumentDirection::includes( BusinessDocumentDirection::PURCHASE ) );
        $this->assertFalse( BusinessDocumentDirection::includes( 'unknown' ) );
    }
}
