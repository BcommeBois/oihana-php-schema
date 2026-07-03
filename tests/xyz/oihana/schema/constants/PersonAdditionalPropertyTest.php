<?php

namespace tests\xyz\oihana\schema\constants ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\PersonAdditionalProperty;

class PersonAdditionalPropertyTest extends TestCase
{
    public function testIncludesKnownProperties(): void
    {
        $this->assertTrue( PersonAdditionalProperty::includes( PersonAdditionalProperty::IS_QUOTE_RECIPIENT ) );
        $this->assertTrue( PersonAdditionalProperty::includes( PersonAdditionalProperty::SHOW_APPLICATIONS  ) );
    }

    public function testIncludesRejectsUnknownProperty(): void
    {
        $this->assertFalse( PersonAdditionalProperty::includes( 'unknownProperty' ) );
    }

    public function testNormalizeCastsBooleanProperties(): void
    {
        $this->assertTrue( PersonAdditionalProperty::normalize( PersonAdditionalProperty::IS_DOCUMENT_RECIPIENT      , '1' ) );
        $this->assertTrue( PersonAdditionalProperty::normalize( PersonAdditionalProperty::IS_QUOTE_RECIPIENT         , '1' ) );
        $this->assertTrue( PersonAdditionalProperty::normalize( PersonAdditionalProperty::IS_DELIVERY_NOTE_RECIPIENT , '1' ) );
        $this->assertTrue( PersonAdditionalProperty::normalize( PersonAdditionalProperty::IS_ORDER_RECIPIENT         , '1' ) );
        $this->assertTrue( PersonAdditionalProperty::normalize( PersonAdditionalProperty::IS_INVOICE_RECIPIENT       , '1' ) );
        $this->assertTrue( PersonAdditionalProperty::normalize( PersonAdditionalProperty::SHOW_APPLICATIONS          , '1' ) );

        $this->assertFalse( PersonAdditionalProperty::normalize( PersonAdditionalProperty::IS_QUOTE_RECIPIENT , '' ) );
    }

    public function testNormalizeReturnsOtherValuesUnchanged(): void
    {
        $this->assertSame( 'value' , PersonAdditionalProperty::normalize( 'anything' , 'value' ) );
    }
}
