<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\ShippingChargeTiming;

class ShippingChargeTimingTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new ShippingChargeTiming() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/ShippingChargeTiming#AtDelivery' , ShippingChargeTiming::AT_DELIVERY );
        $this->assertSame( 'https://schema.oihana.xyz/ShippingChargeTiming#AtOrder'    , ShippingChargeTiming::AT_ORDER );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( ShippingChargeTiming::includes( ShippingChargeTiming::AT_ORDER ) );
        $this->assertFalse( ShippingChargeTiming::includes( 'unknown' ) );
    }
}
