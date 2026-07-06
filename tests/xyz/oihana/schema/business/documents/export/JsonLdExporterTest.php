<?php

namespace tests\xyz\oihana\schema\business\documents\export ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\business\documents\Quote;
use xyz\oihana\schema\business\documents\export\BusinessDocumentExporter;
use xyz\oihana\schema\business\documents\export\JsonLdExporter;
use xyz\oihana\schema\enumerations\BusinessDocumentStatus;

class JsonLdExporterTest extends TestCase
{
    public function testImplementsBusinessDocumentExporter(): void
    {
        $this->assertInstanceOf( BusinessDocumentExporter::class , new JsonLdExporter() );
    }

    public function testExportReturnsJsonLd(): void
    {
        $quote = new Quote
        ([
            Quote::CURRENCY      => 'EUR' ,
            Quote::ISSUE_DATE    => '2026-01-15' ,
            Quote::VALID_THROUGH => '2026-02-15' ,
            Quote::STATUS        => BusinessDocumentStatus::DRAFT ,
        ]);

        $json = new JsonLdExporter()->export( $quote ) ;
        $data = json_decode( $json , true ) ;

        $this->assertIsString( $json ) ;
        $this->assertSame( 'Quote' , $data[ '@type' ] ) ;
        $this->assertSame( 'https://schema.oihana.xyz' , $data[ '@context' ] ) ;
        $this->assertSame( 'EUR' , $data[ Quote::CURRENCY ] ) ;
        $this->assertSame( '2026-02-15' , $data[ Quote::VALID_THROUGH ] ) ;
        $this->assertSame( BusinessDocumentStatus::DRAFT , $data[ Quote::STATUS ] ) ;
    }
}
