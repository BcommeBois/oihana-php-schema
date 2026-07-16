<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\PricingAreaScope;

class PricingAreaScopeTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new PricingAreaScope() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/PricingAreaScope#All'       , PricingAreaScope::ALL       );
        $this->assertSame( 'https://schema.oihana.xyz/PricingAreaScope#Company'   , PricingAreaScope::COMPANY   );
        $this->assertSame( 'https://schema.oihana.xyz/PricingAreaScope#Group'     , PricingAreaScope::GROUP     );
        $this->assertSame( 'https://schema.oihana.xyz/PricingAreaScope#Warehouse' , PricingAreaScope::WAREHOUSE );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( PricingAreaScope::includes( PricingAreaScope::WAREHOUSE ) );
        $this->assertFalse( PricingAreaScope::includes( 'unknown' ) );
    }
}
