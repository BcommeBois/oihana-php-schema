<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;
use org\schema\QuantitativeValue;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\BusinessDocumentLine;
use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\UnitOfSaleType;

class BusinessDocumentLineTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new BusinessDocumentLine() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , BusinessDocumentLine::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'adjustments' , BusinessDocumentLine::ADJUSTMENTS );
        $this->assertSame( 'item'        , BusinessDocumentLine::ITEM        );
        $this->assertSame( 'position'    , BusinessDocumentLine::POSITION    );
        $this->assertSame( 'price'       , BusinessDocumentLine::PRICE       );
        $this->assertSame( 'quantity'    , BusinessDocumentLine::QUANTITY    );
        $this->assertSame( 'subtotal'    , BusinessDocumentLine::SUBTOTAL    );
        $this->assertSame( 'taxes'       , BusinessDocumentLine::TAXES       );
        $this->assertSame( 'total'       , BusinessDocumentLine::TOTAL       );
        $this->assertSame( 'unit'        , BusinessDocumentLine::UNIT        );

        $this->assertSame( Oihana::ADJUSTMENTS , BusinessDocumentLine::ADJUSTMENTS );
    }

    public function testDefaults(): void
    {
        $line = new BusinessDocumentLine() ;

        $this->assertNull( $line->adjustments ?? null );
        $this->assertNull( $line->item        ?? null );
        $this->assertNull( $line->position    ?? null );
        $this->assertNull( $line->price       ?? null );
        $this->assertNull( $line->quantity    ?? null );
        $this->assertNull( $line->subtotal    ?? null );
        $this->assertNull( $line->taxes       ?? null );
        $this->assertNull( $line->total       ?? null );
        $this->assertNull( $line->unit        ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::POSITION => 1 ,
            BusinessDocumentLine::QUANTITY => 5 ,
            BusinessDocumentLine::UNIT     => UnitOfSaleType::UNIT ,
        ]);

        $this->assertSame( 1 , $line->position ) ;
        $this->assertSame( 5 , $line->quantity ) ;
        $this->assertSame( UnitOfSaleType::UNIT , $line->unit ) ;
    }

    public function testConstructorKeepsPriceAsRawArray(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::PRICE => [ 'value' => 20 , 'currency' => 'EUR' ] ,
        ]);

        $this->assertIsArray( $line->price ) ;
    }

    public function testConstructorKeepsQuantityAsRawArray(): void
    {
        $line = new BusinessDocumentLine
        ([
            BusinessDocumentLine::QUANTITY => [ 'value' => 5 , 'unitCode' => 'C62' ] ,
        ]);

        $this->assertIsArray( $line->quantity ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTaxesAndAdjustmentsIntoObjects(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::TAXES       => [ [ 'category' => 'VAT' , 'rate' => 20.0 ] ] ,
                BusinessDocumentLine::ADJUSTMENTS => [ [ 'type' => 'discount' , 'percentage' => 10.0 ] ] ,
                BusinessDocumentLine::SUBTOTAL    => [ 'value' => 100 , 'currency' => 'EUR' ] ,
                BusinessDocumentLine::TOTAL       => [ 'value' => 108 , 'currency' => 'EUR' ] ,
                BusinessDocumentLine::QUANTITY    => [ 'value' => 5 , 'unitCode' => 'C62' ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertInstanceOf( TaxDetail::class , $line->taxes[ 0 ] ) ;
        $this->assertSame( 'VAT' , $line->taxes[ 0 ]->category ) ;

        $this->assertInstanceOf( Adjustment::class , $line->adjustments[ 0 ] ) ;
        $this->assertSame( 10.0 , $line->adjustments[ 0 ]->percentage ) ;

        $this->assertInstanceOf( MonetaryAmount::class , $line->subtotal ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $line->total ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $line->quantity ) ;
        $this->assertSame( 5 , $line->quantity->value ) ;
    }
}
