<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\QuantityOrigin;

class QuantityOriginTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new QuantityOrigin() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/QuantityOrigin#Calculated' , QuantityOrigin::CALCULATED );
        $this->assertSame( 'https://schema.oihana.xyz/QuantityOrigin#Entered'    , QuantityOrigin::ENTERED    );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( QuantityOrigin::includes( QuantityOrigin::CALCULATED ) );
        $this->assertTrue ( QuantityOrigin::includes( QuantityOrigin::ENTERED    ) );
        $this->assertFalse( QuantityOrigin::includes( 'calculated' ) );
    }
}
