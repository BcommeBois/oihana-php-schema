<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\PricingItemScope;

class PricingItemScopeTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new PricingItemScope() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/PricingItemScope#All'      , PricingItemScope::ALL      );
        $this->assertSame( 'https://schema.oihana.xyz/PricingItemScope#Category' , PricingItemScope::CATEGORY );
        $this->assertSame( 'https://schema.oihana.xyz/PricingItemScope#Product'  , PricingItemScope::PRODUCT  );
        $this->assertSame( 'https://schema.oihana.xyz/PricingItemScope#Provider' , PricingItemScope::PROVIDER );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( PricingItemScope::includes( PricingItemScope::PRODUCT ) );
        $this->assertFalse( PricingItemScope::includes( 'unknown' ) );
    }
}
