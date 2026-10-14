<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\QuantitativeValue;
use org\schema\Service;

use xyz\oihana\schema\business\documents\DeliveryLine;
use xyz\oihana\schema\products\Product;

use function xyz\oihana\schema\helpers\hydrate\documents\hydrateDeliveryLine;

/**
 * What a delivery line is worth once hydrated.
 *
 * 🚨 **The list essay is the one that matters.** `DeliveryLine` descends from
 * `Thing`, so the constructor route folds a whole list into a single object — which
 * is exactly the defect this function exists to answer. A function that kept that
 * behaviour would satisfy every other assertion here.
 */
final class HydrateDeliveryLineTest extends TestCase
{
    /**
     * Everything a line measures describes what actually LEFT, and the three figures
     * are quantitative values rather than bare numbers.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesASingleLineWithItsMeasures(): void
    {
        $line = hydrateDeliveryLine(
        [
            'position'          => 1 ,
            'orderedQuantity'   => [ 'value' => 120 , 'unitCode' => 'MTK' ] ,
            'deliveredQuantity' => [ 'value' => 84  , 'unitCode' => 'MTK' ] ,
            'backorderQuantity' => [ 'value' => 36  , 'unitCode' => 'MTK' ] ,
            'backorderReason'   => 'out of stock' ,
            'weight'            => [ 'value' => 512.5 , 'unitCode' => 'KGM' ] ,
            'volume'            => [ 'value' => 1.25  , 'unitCode' => 'MTQ' ] ,
            'batchNumber'       => 'L-2026-07' ,
        ]) ;

        $this->assertInstanceOf( DeliveryLine::class , $line ) ;
        $this->assertSame( 1 , $line->position ) ;
        $this->assertSame( 'out of stock' , $line->backorderReason ) ;
        $this->assertSame( 'L-2026-07' , $line->batchNumber ) ;

        foreach ( [ 'orderedQuantity' , 'deliveredQuantity' , 'backorderQuantity' , 'weight' , 'volume' ] as $measure )
        {
            $this->assertInstanceOf( QuantitativeValue::class , $line->{ $measure } , $measure ) ;
        }

        $this->assertSame( 120 , $line->orderedQuantity->value ) ;
        $this->assertSame( 84  , $line->deliveredQuantity->value ) ;
    }

    /**
     * 🚨 A list is hydrated element by element, which the constructor route cannot
     * do : it would answer one `DeliveryLine` built from the whole list, and a
     * consumer reading `$lines[0]->position` would get nothing.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAListElementByElement(): void
    {
        $lines = hydrateDeliveryLine(
        [
            [ 'position' => 1 , 'deliveredQuantity' => [ 'value' => 2 , 'unitCode' => 'C62' ] ] ,
            [ 'position' => 2 , 'weight'            => [ 'value' => 40 , 'unitCode' => 'KGM' ] ] ,
        ]) ;

        $this->assertIsArray( $lines ) ;
        $this->assertCount( 2 , $lines ) ;
        $this->assertContainsOnlyInstancesOf( DeliveryLine::class , $lines ) ;

        $this->assertSame( 1 , $lines[ 0 ]->position ) ;
        $this->assertSame( 2 , $lines[ 1 ]->position ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $lines[ 0 ]->deliveredQuantity ) ;
        $this->assertInstanceOf( QuantitativeValue::class , $lines[ 1 ]->weight ) ;
    }

    /**
     * The item is resolved on the payload's `@type`, not on the property's declared
     * union — the same rule, and the same helper, as a document line.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testResolvesTheItemOnItsType(): void
    {
        $product = hydrateDeliveryLine( [ 'item' => [ 'name' => 'Panneau contreplaqué' ] ] ) ;
        $service = hydrateDeliveryLine( [ 'item' => [ '@type' => 'Service' , 'name' => 'Pose de sol' ] ] ) ;

        $this->assertInstanceOf( Product::class , $product->item ) ;
        $this->assertInstanceOf( Service::class , $service->item ) ;
        $this->assertSame( 'Pose de sol' , $service->item->name ) ;
    }

    /**
     * 🔑 The order and the invoice a line answers to : their union names a single
     * class each, which the reflection route resolves on its own — and a bare number
     * is left exactly as it was read, because a source commonly states the number of
     * an order it does not hold.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testTheOrderAndTheInvoiceSurviveAsTheyWereRead(): void
    {
        $line = hydrateDeliveryLine(
        [
            'position'          => 1 ,
            'referencesOrder'   => [ 'id' => '1176623' ] ,
            'referencesInvoice' => '260300457' ,
        ]) ;

        $this->assertInstanceOf( DeliveryLine::class , $line ) ;
        $this->assertSame( '260300457' , $line->referencesInvoice ) ;
        $this->assertNotNull( $line->referencesOrder ) ;
    }

    /**
     * 🔑 « This note has no line » is an answer, and it is not the answer « nothing
     * here was readable ». The empty list survives for a consumer to map over.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testAnEmptyListStaysAnEmptyList(): void
    {
        $this->assertSame( [] , hydrateDeliveryLine( [] ) ) ;
    }

    /**
     * 🔑 A bare reference survives inside a list, and the keys stay gap-free : a
     * filtered list left with holes serializes as a JSON object, and a consumer
     * walking the value gets something it cannot walk.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testABareReferenceSurvivesAndTheKeysStayGapFree(): void
    {
        $this->assertSame( [ 'line-ref-42' ] , hydrateDeliveryLine( [ 'line-ref-42' ] ) ) ;

        $mixed = hydrateDeliveryLine( [ null , [ 'position' => 2 ] ] ) ;

        $this->assertIsArray( $mixed ) ;
        $this->assertSame( [ 0 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 2 , $mixed[ 0 ]->position ) ;

        // A non-empty list that hydrates to nothing keeps the family's null — there,
        // nothing usable was found, which is a different statement from « none ».
        $this->assertNull( hydrateDeliveryLine( [ null ] ) ) ;
    }

    /**
     * Anything that is not an array is handed back as it came, and an already
     * hydrated line is not rebuilt.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testWhatIsNotAnArrayIsHandedBack(): void
    {
        $this->assertNull( hydrateDeliveryLine( null ) ) ;
        $this->assertSame( 'delivery-line-7' , hydrateDeliveryLine( 'delivery-line-7' ) ) ;

        $line = new DeliveryLine( [ 'position' => 1 ] ) ;

        $this->assertSame( $line , hydrateDeliveryLine( $line ) ) ;
        $this->assertSame( [ $line ] , hydrateDeliveryLine( [ $line ] ) ) ;
    }
}
