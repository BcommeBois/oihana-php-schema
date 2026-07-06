<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\enumerations\StatusEnumeration;
use xyz\oihana\schema\enumerations\BusinessDocumentStatus;

class BusinessDocumentStatusTest extends TestCase
{
    public function testIsStatusEnumeration(): void
    {
        $this->assertInstanceOf( StatusEnumeration::class , new BusinessDocumentStatus() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentStatus#Accepted'  , BusinessDocumentStatus::ACCEPTED  );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentStatus#Cancelled' , BusinessDocumentStatus::CANCELLED );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentStatus#Converted' , BusinessDocumentStatus::CONVERTED );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentStatus#Draft'     , BusinessDocumentStatus::DRAFT     );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentStatus#Expired'   , BusinessDocumentStatus::EXPIRED   );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentStatus#Rejected'  , BusinessDocumentStatus::REJECTED  );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentStatus#Sent'      , BusinessDocumentStatus::SENT      );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( BusinessDocumentStatus::includes( BusinessDocumentStatus::DRAFT ) );
        $this->assertFalse( BusinessDocumentStatus::includes( 'unknown' ) );
    }
}
