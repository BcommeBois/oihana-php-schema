<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\StatementEntry;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\StatementEntryType;

class StatementEntryTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new StatementEntry() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , StatementEntry::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'amount'       , StatementEntry::AMOUNT        );
        $this->assertSame( 'balance'      , StatementEntry::BALANCE       );
        $this->assertSame( 'creditAmount' , StatementEntry::CREDIT_AMOUNT );
        $this->assertSame( 'date'         , StatementEntry::DATE          );
        $this->assertSame( 'debitAmount'  , StatementEntry::DEBIT_AMOUNT  );
        $this->assertSame( 'document'     , StatementEntry::DOCUMENT      );
        $this->assertSame( 'dueDate'      , StatementEntry::DUE_DATE      );
        $this->assertSame( 'type'         , StatementEntry::TYPE          );

        $this->assertSame( Oihana::AMOUNT , StatementEntry::AMOUNT );
    }

    public function testDefaults(): void
    {
        $entry = new StatementEntry() ;

        $this->assertNull( $entry->amount       ?? null );
        $this->assertNull( $entry->balance      ?? null );
        $this->assertNull( $entry->creditAmount ?? null );
        $this->assertNull( $entry->date         ?? null );
        $this->assertNull( $entry->debitAmount  ?? null );
        $this->assertNull( $entry->document     ?? null );
        $this->assertNull( $entry->dueDate      ?? null );
        $this->assertNull( $entry->type         ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $entry = new StatementEntry
        ([
            StatementEntry::DATE     => '2026-01-15' ,
            StatementEntry::DUE_DATE => '2026-02-15' ,
            StatementEntry::DOCUMENT => 'INV-001' ,
            StatementEntry::TYPE     => StatementEntryType::INVOICE ,
        ]);

        $this->assertSame( '2026-01-15' , $entry->date ) ;
        $this->assertSame( '2026-02-15' , $entry->dueDate ) ;
        $this->assertSame( 'INV-001' , $entry->document ) ;
        $this->assertSame( StatementEntryType::INVOICE , $entry->type ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesNestedValueObjects(): void
    {
        $entry = new Reflection()->hydrate
        (
            [
                StatementEntry::AMOUNT       => [ 'value' => 100 , 'currency' => 'EUR' ] ,
                StatementEntry::BALANCE      => [ 'value' => 900 , 'currency' => 'EUR' ] ,
                StatementEntry::DEBIT_AMOUNT => [ 'value' => 100 , 'currency' => 'EUR' ] ,
                StatementEntry::DOCUMENT     => [ BusinessDocument::CURRENCY => 'EUR' ] ,
            ],
            StatementEntry::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $entry->amount ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $entry->balance ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $entry->debitAmount ) ;
        $this->assertInstanceOf( BusinessDocument::class , $entry->document ) ;
    }
}
