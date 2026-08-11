<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\BusinessDocumentAuthority;

class BusinessDocumentAuthorityTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new BusinessDocumentAuthority() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentAuthority#Owned'    , BusinessDocumentAuthority::OWNED    );
        $this->assertSame( 'https://schema.oihana.xyz/BusinessDocumentAuthority#Mirrored' , BusinessDocumentAuthority::MIRRORED );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( BusinessDocumentAuthority::includes( BusinessDocumentAuthority::OWNED    ) );
        $this->assertTrue ( BusinessDocumentAuthority::includes( BusinessDocumentAuthority::MIRRORED ) );
        $this->assertFalse( BusinessDocumentAuthority::includes( 'unknown' ) );
    }
}
