<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Product;
use org\schema\QuantitativeValue;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\GoodsReceiptLine;
use xyz\oihana\schema\constants\Oihana;

class GoodsReceiptLineTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new GoodsReceiptLine() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , GoodsReceiptLine::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'condition'        , GoodsReceiptLine::CONDITION         );
        $this->assertSame( 'discrepancyNote'  , GoodsReceiptLine::DISCREPANCY_NOTE  );
        $this->assertSame( 'expectedQuantity' , GoodsReceiptLine::EXPECTED_QUANTITY );
        $this->assertSame( 'item'             , GoodsReceiptLine::ITEM              );
        $this->assertSame( 'position'         , GoodsReceiptLine::POSITION          );
        $this->assertSame( 'receivedQuantity' , GoodsReceiptLine::RECEIVED_QUANTITY );

        $this->assertSame( Oihana::POSITION , GoodsReceiptLine::POSITION );
    }

    public function testDefaults(): void
    {
        $line = new GoodsReceiptLine() ;

        $this->assertNull( $line->condition        ?? null );
        $this->assertNull( $line->discrepancyNote  ?? null );
        $this->assertNull( $line->expectedQuantity ?? null );
        $this->assertNull( $line->item             ?? null );
        $this->assertNull( $line->position         ?? null );
        $this->assertNull( $line->receivedQuantity ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $line = new GoodsReceiptLine
        ([
            GoodsReceiptLine::POSITION         => 1 ,
            GoodsReceiptLine::CONDITION        => 'damaged' ,
            GoodsReceiptLine::DISCREPANCY_NOTE => '2 units broken' ,
        ]);

        $this->assertSame( 1 , $line->position ) ;
        $this->assertSame( 'damaged' , $line->condition ) ;
        $this->assertSame( '2 units broken' , $line->discrepancyNote ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesNestedValueObjects(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                GoodsReceiptLine::ITEM             => [ 'name' => 'Widget' ] ,
                GoodsReceiptLine::EXPECTED_QUANTITY => [ 'value' => 100 , 'unitCode' => 'EA' ] ,
                GoodsReceiptLine::RECEIVED_QUANTITY => [ 'value' => 98  , 'unitCode' => 'EA' ] ,
            ],
            GoodsReceiptLine::class
        );

        $this->assertInstanceOf( Product::class , $line->item ) ;
        $this->assertInstanceOf( QuantitativeValue::class , $line->expectedQuantity ) ;
        $this->assertInstanceOf( QuantitativeValue::class , $line->receivedQuantity ) ;
        $this->assertSame( 98 , $line->receivedQuantity->value ) ;
    }
}
