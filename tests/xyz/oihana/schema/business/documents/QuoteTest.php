<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\Quote;
use xyz\oihana\schema\constants\Oihana;

class QuoteTest extends TestCase
{
    public function testIsBusinessDocument(): void
    {
        $this->assertInstanceOf( BusinessDocument::class , new Quote() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , Quote::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'validThrough' , Quote::VALID_THROUGH );
        $this->assertSame( Oihana::VALID_THROUGH , Quote::VALID_THROUGH );
    }

    public function testDefaults(): void
    {
        $quote = new Quote() ;

        $this->assertNull( $quote->validThrough ?? null );
    }

    public function testConstructorHydratesValidThrough(): void
    {
        $quote = new Quote([ Quote::VALID_THROUGH => '2026-06-30' ]) ;

        $this->assertSame( '2026-06-30' , $quote->validThrough ) ;
    }

    public function testInheritsBusinessDocumentProperties(): void
    {
        $quote = new Quote
        ([
            BusinessDocument::CURRENCY => 'EUR' ,
            Quote::VALID_THROUGH       => '2026-06-30' ,
        ]);

        $this->assertSame( 'EUR' , $quote->currency ) ;
        $this->assertSame( '2026-06-30' , $quote->validThrough ) ;
    }
}
