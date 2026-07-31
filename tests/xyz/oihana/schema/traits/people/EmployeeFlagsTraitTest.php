<?php

namespace tests\xyz\oihana\schema\traits\people ;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\PersonAdditionalProperty;
use xyz\oihana\schema\traits\people\EmployeeFlagsTrait;

/**
 * Host exposing {@see EmployeeFlagsTrait}, mirroring how
 * {@see \xyz\oihana\schema\people\Person} uses it.
 */
class EmployeeFlagsHost
{
    use EmployeeFlagsTrait ;

    public null|array $additionalProperty = null ;
}

class EmployeeFlagsTraitTest extends TestCase
{
    #[DataProvider( 'flagsProvider' )]
    public function testFlagReflectsTheMatchingAdditionalProperty( string $propertyID , string $method )
    {
        $host = new EmployeeFlagsHost() ;

        $this->assertFalse( $host->$method() ) ;

        $host->additionalProperty = [ [ 'propertyID' => $propertyID , 'value' => true ] ] ;

        $this->assertTrue( $host->$method() ) ;
    }

    public static function flagsProvider(): array
    {
        return
        [
            'delivery note recipient' => [ PersonAdditionalProperty::IS_DELIVERY_NOTE_RECIPIENT , 'isDeliveryNoteRecipient' ] ,
            'document recipient'      => [ PersonAdditionalProperty::IS_DOCUMENT_RECIPIENT      , 'isDocumentRecipient'      ] ,
            'invoice recipient'       => [ PersonAdditionalProperty::IS_INVOICE_RECIPIENT        , 'isInvoiceRecipient'       ] ,
            'order recipient'         => [ PersonAdditionalProperty::IS_ORDER_RECIPIENT          , 'isOrderRecipient'         ] ,
            'quote recipient'         => [ PersonAdditionalProperty::IS_QUOTE_RECIPIENT           , 'isQuoteRecipient'         ] ,
            'shows applications'      => [ PersonAdditionalProperty::SHOW_APPLICATIONS            , 'showsApplications'        ] ,
        ] ;
    }
}
