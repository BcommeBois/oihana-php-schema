<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\CreditNote;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\constants\Oihana;

class CreditNoteTest extends TestCase
{
    public function testIsBusinessDocument(): void
    {
        $this->assertInstanceOf( BusinessDocument::class , new CreditNote() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , CreditNote::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'reason'            , CreditNote::REASON             );
        $this->assertSame( 'referencesInvoice' , CreditNote::REFERENCES_INVOICE );

        $this->assertSame( Oihana::REASON , CreditNote::REASON );
    }

    public function testDefaults(): void
    {
        $creditNote = new CreditNote() ;

        $this->assertNull( $creditNote->reason            ?? null );
        $this->assertNull( $creditNote->referencesInvoice ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $creditNote = new CreditNote([ CreditNote::REASON => 'Goods returned' ]) ;

        $this->assertSame( 'Goods returned' , $creditNote->reason ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesReferencesInvoice(): void
    {
        $creditNote = new Reflection()->hydrate
        (
            [
                CreditNote::REFERENCES_INVOICE => [ BusinessDocument::CURRENCY => 'EUR' ] ,
            ],
            CreditNote::class
        );

        $this->assertInstanceOf( Invoice::class , $creditNote->referencesInvoice ) ;
        $this->assertSame( 'EUR' , $creditNote->referencesInvoice->currency ) ;
    }

    public function testInheritsBusinessDocumentProperties(): void
    {
        $creditNote = new CreditNote
        ([
            BusinessDocument::CURRENCY => 'EUR' ,
            CreditNote::REASON         => 'Goods returned' ,
        ]);

        $this->assertSame( 'EUR' , $creditNote->currency ) ;
        $this->assertSame( 'Goods returned' , $creditNote->reason ) ;
    }
}
