<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\DebitNote;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\constants\Oihana;

class DebitNoteTest extends TestCase
{
    public function testIsBusinessDocument(): void
    {
        $this->assertInstanceOf( BusinessDocument::class , new DebitNote() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , DebitNote::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'reason'            , DebitNote::REASON             );
        $this->assertSame( 'referencesInvoice' , DebitNote::REFERENCES_INVOICE );

        $this->assertSame( Oihana::REASON , DebitNote::REASON );
    }

    public function testDefaults(): void
    {
        $debitNote = new DebitNote() ;

        $this->assertNull( $debitNote->reason            ?? null );
        $this->assertNull( $debitNote->referencesInvoice ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $debitNote = new DebitNote([ DebitNote::REASON => 'Under-billed' ]) ;

        $this->assertSame( 'Under-billed' , $debitNote->reason ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesReferencesInvoiceList(): void
    {
        $debitNote = new Reflection()->hydrate
        (
            [
                DebitNote::REFERENCES_INVOICE =>
                [
                    [ BusinessDocument::CURRENCY => 'EUR' ] ,
                    [ BusinessDocument::CURRENCY => 'USD' ] ,
                ] ,
            ],
            DebitNote::class
        );

        $this->assertIsArray( $debitNote->referencesInvoice ) ;
        $this->assertCount( 2 , $debitNote->referencesInvoice ) ;
        $this->assertInstanceOf( Invoice::class , $debitNote->referencesInvoice[ 0 ] ) ;
        $this->assertSame( 'USD' , $debitNote->referencesInvoice[ 1 ]->currency ) ;
    }

    public function testInheritsBusinessDocumentProperties(): void
    {
        $debitNote = new DebitNote([ BusinessDocument::CURRENCY => 'EUR' ]) ;

        $this->assertSame( 'EUR' , $debitNote->currency ) ;
    }
}
