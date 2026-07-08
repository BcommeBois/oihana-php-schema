<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\PricingTargetScope;

class PricingTargetScopeTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new PricingTargetScope() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/PricingTargetScope#All'        , PricingTargetScope::ALL        );
        $this->assertSame( 'https://schema.oihana.xyz/PricingTargetScope#Company'    , PricingTargetScope::COMPANY    );
        $this->assertSame( 'https://schema.oihana.xyz/PricingTargetScope#Group'      , PricingTargetScope::GROUP      );
        $this->assertSame( 'https://schema.oihana.xyz/PricingTargetScope#Individual' , PricingTargetScope::INDIVIDUAL );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( PricingTargetScope::includes( PricingTargetScope::GROUP ) );
        $this->assertFalse( PricingTargetScope::includes( 'unknown' ) );
    }
}
