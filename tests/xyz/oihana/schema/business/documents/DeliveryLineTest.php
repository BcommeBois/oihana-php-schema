<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Product;
use org\schema\QuantitativeValue;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\DeliveryLine;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\business\documents\PurchaseOrder;
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
        $this->assertSame( 'referencesInvoice' , DeliveryLine::REFERENCES_INVOICE );
        $this->assertSame( 'referencesOrder'   , DeliveryLine::REFERENCES_ORDER   );
        $this->assertSame( 'serialNumbers'     , DeliveryLine::SERIAL_NUMBERS     );
        $this->assertSame( 'volume'            , DeliveryLine::VOLUME             );
        $this->assertSame( 'weight'            , DeliveryLine::WEIGHT             );

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
        $this->assertNull( $line->referencesInvoice ?? null );
        $this->assertNull( $line->referencesOrder   ?? null );
        $this->assertNull( $line->serialNumbers     ?? null );
        $this->assertNull( $line->volume            ?? null );
        $this->assertNull( $line->weight            ?? null );
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

    /**
     * A bare order reference is left as read : a note that mixes several orders
     * needs to name one per line, and a key is often all the source gives.
     * @throws ReflectionException
     */
    public function testReferencesOrderKeepsABareReference(): void
    {
        $line = new DeliveryLine
        ([
            DeliveryLine::POSITION         => 325       ,
            DeliveryLine::REFERENCES_ORDER => '1142229' ,
        ]);

        $this->assertSame( '1142229' , $line->referencesOrder ) ;
        $this->assertSame( 325       , $line->position        ) ;
    }

    /**
     * The union names a single class, so a joined row resolves on its own.
     * @throws ReflectionException
     */
    public function testReflectionResolvesTheOriginatingOrder(): void
    {
        $line = new Reflection()->hydrate
        (
            [ DeliveryLine::REFERENCES_ORDER => [ 'identifier' => '1142229' ] ],
            DeliveryLine::class
        );

        $this->assertInstanceOf( PurchaseOrder::class , $line->referencesOrder ) ;
        $this->assertSame( '1142229' , $line->referencesOrder->identifier ) ;
    }

    /**
     * The constructor assigns raw — `Reflection::hydrate()` alone resolves the
     * union — so a joined row has to be able to sit in the property until it
     * does, exactly as it does on every sibling reference of the namespace.
     */
    public function testConstructorKeepsAJoinedOrderAsRawArray(): void
    {
        $line = new DeliveryLine([ DeliveryLine::REFERENCES_ORDER => [ 'identifier' => '1142229' ] ]) ;

        $this->assertIsArray( $line->referencesOrder ) ;
        $this->assertSame( '1142229' , $line->referencesOrder[ 'identifier' ] ) ;
    }

    /**
     * The invoice sits on the line and not on the note, so two lines of the
     * same note can answer to two invoices — which is what a note delivering
     * several orders does.
     *
     * @throws ReflectionException
     */
    public function testTwoLinesOfOneNoteCanBeBilledByTwoInvoices(): void
    {
        $first = new Reflection()->hydrate
        (
            [
                DeliveryLine::POSITION           => 1 ,
                DeliveryLine::REFERENCES_ORDER   => [ 'identifier' => 'CDE-1148902' ] ,
                DeliveryLine::REFERENCES_INVOICE => [ 'identifier' => 'INV-2026-04417' ] ,
            ],
            DeliveryLine::class
        );

        $second = new Reflection()->hydrate
        (
            [
                DeliveryLine::POSITION           => 1 ,
                DeliveryLine::REFERENCES_ORDER   => [ 'identifier' => 'CDE-1149355' ] ,
                DeliveryLine::REFERENCES_INVOICE => [ 'identifier' => 'INV-2026-04418' ] ,
            ],
            DeliveryLine::class
        );

        $this->assertInstanceOf( Invoice::class , $first->referencesInvoice  ) ;
        $this->assertInstanceOf( Invoice::class , $second->referencesInvoice ) ;

        // same position, two orders, two invoices : the position alone names nothing
        $this->assertSame( $first->position , $second->position ) ;
        $this->assertNotSame
        (
            $first->referencesInvoice->identifier ,
            $second->referencesInvoice->identifier
        ) ;
    }

    /**
     * A bare invoice reference is left as read, like a bare order reference.
     */
    public function testReferencesInvoiceKeepsABareReference(): void
    {
        $line = new DeliveryLine([ DeliveryLine::REFERENCES_INVOICE => 'INV-2026-04417' ]) ;

        $this->assertSame( 'INV-2026-04417' , $line->referencesInvoice ) ;
    }

    /**
     * What a line weighs and what it occupies is what LEFT, not what was
     * ordered : a line delivering 84 of the 120 square meters ordered weighs
     * the 84.
     *
     * @throws ReflectionException
     */
    public function testTheLineMeasuresWhatWasDeliveredAndSumsToTheNote(): void
    {
        $lines =
        [
            new DeliveryLine
            ([
                DeliveryLine::ORDERED_QUANTITY   => 120 ,
                DeliveryLine::DELIVERED_QUANTITY => 84 ,
                DeliveryLine::WEIGHT             => 537.6 ,   // 84 × 6.4, not 120 × 6.4
                DeliveryLine::VOLUME             => 1.176 ,   // 84 × 0.014, not 120 × 0.014
            ]) ,
            new DeliveryLine
            ([
                DeliveryLine::DELIVERED_QUANTITY => 1467 ,
                DeliveryLine::WEIGHT             => 1193.775 ,
                DeliveryLine::VOLUME             => 2.236 ,
            ]) ,
        ];

        $this->assertSame( 537.6    , $lines[ 0 ]->weight ) ;
        $this->assertSame( 1.176    , $lines[ 0 ]->volume ) ;
        $this->assertSame( 1193.775 , $lines[ 1 ]->weight ) ;
        $this->assertSame( 2.236    , $lines[ 1 ]->volume ) ;

        $this->assertSame( 1731.375 , array_sum( array_map( fn( $line ) => $line->weight , $lines ) ) ) ;
        $this->assertSame( 3.412    , array_sum( array_map( fn( $line ) => $line->volume , $lines ) ) ) ;
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
                DeliveryLine::WEIGHT => [ 'value' => 537.6 , 'unitCode' => 'KGM' ] ,
                DeliveryLine::VOLUME => [ 'value' => 1.176 , 'unitCode' => 'MTQ' ] ,
            ],
            DeliveryLine::class
        );

        $this->assertInstanceOf( QuantitativeValue::class , $line->weight ) ;
        $this->assertSame( 537.6 , $line->weight->value    ) ;
        $this->assertSame( 'KGM' , $line->weight->unitCode ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $line->volume ) ;
        $this->assertSame( 1.176 , $line->volume->value    ) ;
        $this->assertSame( 'MTQ' , $line->volume->unitCode ) ;
    }

    /**
     * The two are independent : a note that weighs its lines without measuring
     * their bulk leaves the volume absent, rather than reading as a zero.
     *
     * @throws ReflectionException
     */
    public function testAMeasureLeftOutStaysAbsent(): void
    {
        $line = new Reflection()->hydrate
        (
            [ DeliveryLine::WEIGHT => 537.6 ],
            DeliveryLine::class
        );

        $this->assertSame( 537.6 , $line->weight ) ;
        $this->assertNull( $line->volume ?? null ) ;
    }
}
