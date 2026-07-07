<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Product;
use org\schema\QuantitativeValue;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\DeliveryLine;
use xyz\oihana\schema\constants\Oihana;

class DeliveryLineTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new DeliveryLine() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , DeliveryLine::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'backorderQuantity' , DeliveryLine::BACKORDER_QUANTITY );
        $this->assertSame( 'backorderReason'   , DeliveryLine::BACKORDER_REASON   );
        $this->assertSame( 'batchNumber'       , DeliveryLine::BATCH_NUMBER       );
        $this->assertSame( 'deliveredQuantity' , DeliveryLine::DELIVERED_QUANTITY );
        $this->assertSame( 'item'              , DeliveryLine::ITEM               );
        $this->assertSame( 'orderedQuantity'   , DeliveryLine::ORDERED_QUANTITY   );
        $this->assertSame( 'position'          , DeliveryLine::POSITION           );
        $this->assertSame( 'serialNumbers'     , DeliveryLine::SERIAL_NUMBERS     );

        $this->assertSame( Oihana::POSITION , DeliveryLine::POSITION );
    }

    public function testDefaults(): void
    {
        $line = new DeliveryLine() ;

        $this->assertNull( $line->backorderQuantity ?? null );
        $this->assertNull( $line->backorderReason   ?? null );
        $this->assertNull( $line->batchNumber       ?? null );
        $this->assertNull( $line->deliveredQuantity ?? null );
        $this->assertNull( $line->item              ?? null );
        $this->assertNull( $line->orderedQuantity   ?? null );
        $this->assertNull( $line->position          ?? null );
        $this->assertNull( $line->serialNumbers     ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $line = new DeliveryLine
        ([
            DeliveryLine::POSITION           => 1 ,
            DeliveryLine::ORDERED_QUANTITY   => 100 ,
            DeliveryLine::DELIVERED_QUANTITY => 80 ,
            DeliveryLine::BACKORDER_QUANTITY => 20 ,
            DeliveryLine::BACKORDER_REASON   => 'Out of stock' ,
            DeliveryLine::BATCH_NUMBER       => 'LOT-2026-01' ,
            DeliveryLine::SERIAL_NUMBERS     => [ 'SN-1' , 'SN-2' ] ,
        ]);

        $this->assertSame( 1 , $line->position ) ;
        $this->assertSame( 100 , $line->orderedQuantity ) ;
        $this->assertSame( 80 , $line->deliveredQuantity ) ;
        $this->assertSame( 20 , $line->backorderQuantity ) ;
        $this->assertSame( 'Out of stock' , $line->backorderReason ) ;
        $this->assertSame( 'LOT-2026-01' , $line->batchNumber ) ;
        $this->assertSame( [ 'SN-1' , 'SN-2' ] , $line->serialNumbers ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesNestedValueObjects(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                DeliveryLine::ITEM              => [ 'name' => 'Widget' ] ,
                DeliveryLine::ORDERED_QUANTITY   => [ 'value' => 100 , 'unitCode' => 'EA' ] ,
                DeliveryLine::DELIVERED_QUANTITY => [ 'value' => 80  , 'unitCode' => 'EA' ] ,
            ],
            DeliveryLine::class
        );

        $this->assertInstanceOf( Product::class , $line->item ) ;
        $this->assertInstanceOf( QuantitativeValue::class , $line->orderedQuantity ) ;
        $this->assertInstanceOf( QuantitativeValue::class , $line->deliveredQuantity ) ;
        $this->assertSame( 100 , $line->orderedQuantity->value ) ;
    }
}
