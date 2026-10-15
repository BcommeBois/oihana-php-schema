<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\BusinessDocumentSourceState;

class BusinessDocumentSourceStateTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new BusinessDocumentSourceState() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentSourceState#Converted' , BusinessDocumentSourceState::CONVERTED );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentSourceState#CounterSale' , BusinessDocumentSourceState::COUNTER_SALE );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentSourceState#Delivered' , BusinessDocumentSourceState::DELIVERED );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentSourceState#Removed'   , BusinessDocumentSourceState::REMOVED   );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentSourceState#Replaced'  , BusinessDocumentSourceState::REPLACED  );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentSourceState#Returned'  , BusinessDocumentSourceState::RETURNED  );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( BusinessDocumentSourceState::includes( BusinessDocumentSourceState::CONVERTED ) );
        $this->assertTrue ( BusinessDocumentSourceState::includes( BusinessDocumentSourceState::COUNTER_SALE ) );
        $this->assertTrue ( BusinessDocumentSourceState::includes( BusinessDocumentSourceState::DELIVERED ) );
        $this->assertTrue ( BusinessDocumentSourceState::includes( BusinessDocumentSourceState::REMOVED   ) );
        $this->assertTrue ( BusinessDocumentSourceState::includes( BusinessDocumentSourceState::REPLACED  ) );
        $this->assertTrue ( BusinessDocumentSourceState::includes( BusinessDocumentSourceState::RETURNED  ) );
        $this->assertFalse( BusinessDocumentSourceState::includes( 'unknown' ) );
    }
}
