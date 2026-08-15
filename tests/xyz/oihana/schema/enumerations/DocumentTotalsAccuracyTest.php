<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\DocumentTotalsAccuracy;

class DocumentTotalsAccuracyTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new DocumentTotalsAccuracy() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/DocumentTotalsAccuracy#Exact'   , DocumentTotalsAccuracy::EXACT   );
        $this->assertSame( 'https://schema.oihana.xyz/DocumentTotalsAccuracy#Minimum' , DocumentTotalsAccuracy::MINIMUM );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( DocumentTotalsAccuracy::includes( DocumentTotalsAccuracy::EXACT   ) );
        $this->assertTrue ( DocumentTotalsAccuracy::includes( DocumentTotalsAccuracy::MINIMUM ) );
        $this->assertFalse( DocumentTotalsAccuracy::includes( 'unknown' ) );
    }
}
