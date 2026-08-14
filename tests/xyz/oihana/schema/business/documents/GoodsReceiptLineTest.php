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
        $this->assertSame( 'volume'           , GoodsReceiptLine::VOLUME            );
        $this->assertSame( 'weight'           , GoodsReceiptLine::WEIGHT            );

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
        $this->assertNull( $line->volume           ?? null );
        $this->assertNull( $line->weight           ?? null );
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

    // ---- HasPhysicalMeasures

    /**
     * The receipt measures what was received, the note measures what left. Both
     * sides carry the pair, which is what lets a short delivery be seen as one :
     * 98 of the 100 announced weigh less than the note claimed.
     *
     * @throws ReflectionException
     */
    public function testTheLineMeasuresWhatWasReceived(): void
    {
        $line = new GoodsReceiptLine
        ([
            GoodsReceiptLine::EXPECTED_QUANTITY => 100 ,
            GoodsReceiptLine::RECEIVED_QUANTITY => 98 ,
            GoodsReceiptLine::WEIGHT            => 627.2 ,  // 98 × 6.4, not 100 × 6.4
            GoodsReceiptLine::VOLUME            => 1.372 ,  // 98 × 0.014
        ]);

        $this->assertSame( 627.2 , $line->weight ) ;
        $this->assertSame( 1.372 , $line->volume ) ;
    }

    /**
     * A measure that states its unit comes back typed, so a consumer reads the
     * unit instead of assuming one.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesLineMeasuresThatStateTheirUnit(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                GoodsReceiptLine::WEIGHT => [ 'value' => 627.2 , 'unitCode' => 'KGM' ] ,
                GoodsReceiptLine::VOLUME => [ 'value' => 1.372 , 'unitCode' => 'MTQ' ] ,
            ],
            GoodsReceiptLine::class
        );

        $this->assertInstanceOf( QuantitativeValue::class , $line->weight ) ;
        $this->assertSame( 627.2 , $line->weight->value    ) ;
        $this->assertSame( 'KGM' , $line->weight->unitCode ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $line->volume ) ;
        $this->assertSame( 1.372 , $line->volume->value    ) ;
        $this->assertSame( 'MTQ' , $line->volume->unitCode ) ;
    }

    /**
     * The two are independent : a receipt that weighs its lines without
     * measuring their bulk leaves the volume absent, rather than reading as a
     * zero.
     *
     * @throws ReflectionException
     */
    public function testAMeasureLeftOutStaysAbsent(): void
    {
        $line = new Reflection()->hydrate
        (
            [ GoodsReceiptLine::WEIGHT => 627.2 ],
            GoodsReceiptLine::class
        );

        $this->assertSame( 627.2 , $line->weight ) ;
        $this->assertNull( $line->volume ?? null ) ;
    }
}
