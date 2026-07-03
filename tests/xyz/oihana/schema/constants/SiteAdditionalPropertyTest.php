<?php

namespace tests\xyz\oihana\schema\constants ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\SiteAdditionalProperty;

class SiteAdditionalPropertyTest extends TestCase
{
    public function testIncludesKnownProperties(): void
    {
        $this->assertTrue( SiteAdditionalProperty::includes( SiteAdditionalProperty::IS_DEFAULT_ADDRESS ) );
        $this->assertTrue( SiteAdditionalProperty::includes( SiteAdditionalProperty::DELIVERY_METHOD    ) );
    }

    public function testIncludesRejectsUnknownProperty(): void
    {
        $this->assertFalse( SiteAdditionalProperty::includes( 'unknownProperty' ) );
    }

    public function testNormalizeCastsBooleanProperties(): void
    {
        $this->assertTrue( SiteAdditionalProperty::normalize( SiteAdditionalProperty::IS_BILLING_ADDRESS   , '1' ) );
        $this->assertTrue( SiteAdditionalProperty::normalize( SiteAdditionalProperty::IS_CONSTRUCTION_SITE , '1' ) );
        $this->assertTrue( SiteAdditionalProperty::normalize( SiteAdditionalProperty::IS_DEFAULT_ADDRESS   , '1' ) );
        $this->assertTrue( SiteAdditionalProperty::normalize( SiteAdditionalProperty::IS_DELIVERY_ADDRESS  , '1' ) );
        $this->assertTrue( SiteAdditionalProperty::normalize( SiteAdditionalProperty::IS_SHIPPING_ADDRESS  , '1' ) );

        $this->assertFalse( SiteAdditionalProperty::normalize( SiteAdditionalProperty::IS_DEFAULT_ADDRESS , '' ) );
    }

    public function testNormalizeReturnsOtherValuesUnchanged(): void
    {
        $this->assertSame( 'express' , SiteAdditionalProperty::normalize( SiteAdditionalProperty::DELIVERY_METHOD , 'express' ) );
    }
}
