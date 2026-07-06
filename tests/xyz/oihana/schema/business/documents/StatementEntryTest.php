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
        $this->assertSame( 'amount'   , StatementEntry::AMOUNT   );
        $this->assertSame( 'balance'  , StatementEntry::BALANCE  );
        $this->assertSame( 'date'     , StatementEntry::DATE     );
        $this->assertSame( 'document' , StatementEntry::DOCUMENT );

        $this->assertSame( Oihana::AMOUNT , StatementEntry::AMOUNT );
    }

    public function testDefaults(): void
    {
        $entry = new StatementEntry() ;

        $this->assertNull( $entry->amount   ?? null );
        $this->assertNull( $entry->balance  ?? null );
        $this->assertNull( $entry->date     ?? null );
        $this->assertNull( $entry->document ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $entry = new StatementEntry
        ([
            StatementEntry::DATE     => '2026-01-15' ,
            StatementEntry::DOCUMENT => 'INV-001' ,
        ]);

        $this->assertSame( '2026-01-15' , $entry->date ) ;
        $this->assertSame( 'INV-001' , $entry->document ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesNestedValueObjects(): void
    {
        $entry = new Reflection()->hydrate
        (
            [
                StatementEntry::AMOUNT   => [ 'value' => 100 , 'currency' => 'EUR' ] ,
                StatementEntry::BALANCE  => [ 'value' => 900 , 'currency' => 'EUR' ] ,
                StatementEntry::DOCUMENT => [ BusinessDocument::CURRENCY => 'EUR' ] ,
            ],
            StatementEntry::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $entry->amount ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $entry->balance ) ;
        $this->assertInstanceOf( BusinessDocument::class , $entry->document ) ;
    }
}
