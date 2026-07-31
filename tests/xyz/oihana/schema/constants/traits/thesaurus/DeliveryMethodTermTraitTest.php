<?php

namespace tests\xyz\oihana\schema\constants\traits\thesaurus ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\thesaurus\DeliveryMethodTermTrait;

class DeliveryMethodTermTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use DeliveryMethodTermTrait; };

        $this->assertSame( 'chargeTiming'          , $host::CHARGE_TIMING );
        $this->assertSame( 'freeShippingThreshold' , $host::FREE_SHIPPING_THRESHOLD );
        $this->assertSame( 'shippingRate'          , $host::SHIPPING_RATE );
        $this->assertSame( 'vat'                   , $host::VAT );
    }
}
