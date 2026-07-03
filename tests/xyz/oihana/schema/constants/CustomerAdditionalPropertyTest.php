<?php

namespace tests\xyz\oihana\schema\constants ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\CustomerAdditionalProperty;

class CustomerAdditionalPropertyTest extends TestCase
{
    public function testIncludesKnownProperties(): void
    {
        $this->assertTrue( CustomerAdditionalProperty::includes( CustomerAdditionalProperty::SHOW_APPLICATIONS ) );
        $this->assertTrue( CustomerAdditionalProperty::includes( CustomerAdditionalProperty::INVOICE_ISSUE_INTERVAL ) );
    }

    public function testIncludesRejectsUnknownProperty(): void
    {
        $this->assertFalse( CustomerAdditionalProperty::includes( 'unknownProperty' ) );
    }

    public function testNormalizeCastsBooleanProperties(): void
    {
        $this->assertTrue( CustomerAdditionalProperty::normalize( CustomerAdditionalProperty::GENERATE_ACKNOWLEDGING_RECEIPT , '1' ) );
        $this->assertTrue( CustomerAdditionalProperty::normalize( CustomerAdditionalProperty::ORDER_SHOW_IDENTIFIER          , '1' ) );
        $this->assertTrue( CustomerAdditionalProperty::normalize( CustomerAdditionalProperty::PRINT_AND_MAIL_INVOICE         , '1' ) );
        $this->assertTrue( CustomerAdditionalProperty::normalize( CustomerAdditionalProperty::SHOW_APPLICATIONS              , '1' ) );

        $this->assertFalse( CustomerAdditionalProperty::normalize( CustomerAdditionalProperty::SHOW_APPLICATIONS , '' ) );
    }

    public function testNormalizeCastsInvoiceIssueIntervalToInt(): void
    {
        $this->assertSame( 3 , CustomerAdditionalProperty::normalize( CustomerAdditionalProperty::INVOICE_ISSUE_INTERVAL , '3' ) );
        $this->assertNull( CustomerAdditionalProperty::normalize( CustomerAdditionalProperty::INVOICE_ISSUE_INTERVAL , null ) );
    }

    public function testNormalizeReturnsOtherValuesUnchanged(): void
    {
        $this->assertSame( 'BE0123' , CustomerAdditionalProperty::normalize( CustomerAdditionalProperty::ASSIGNED_COMPANY , 'BE0123' ) );
    }
}
