<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\QuantitativeValue;
use org\schema\Service;

use xyz\oihana\schema\products\Product;
use xyz\oihana\schema\products\StockLevel;

use function xyz\oihana\schema\helpers\hydrate\documents\hydrateDocumentLineItem;

final class HydrateDocumentLineItemTest extends TestCase
{
    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAProductByDefault(): void
    {
        $item = hydrateDocumentLineItem( [ 'name' => 'Peinture blanche' , 'unitOfSale' => 'unit' ] ) ;

        $this->assertInstanceOf( Product::class , $item ) ;
        $this->assertSame( 'Peinture blanche' , $item->name       ) ;
        $this->assertSame( 'unit'             , $item->unitOfSale ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesTheProductNestedReferences(): void
    {
        $item = hydrateDocumentLineItem(
        [
            'name'             => 'Peinture blanche' ,
            'eligibleQuantity' => [ 'value' => 12 , 'unitCode' => 'C62' ] ,
            'inventoryLevel'   => [ 'value' => 120 , 'assignedPOS' => [ 'name' => 'Dépôt A' ] ] ,
        ]) ;

        $this->assertInstanceOf( Product::class , $item ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $item->eligibleQuantity ) ;
        $this->assertSame( 12 , $item->eligibleQuantity->value ) ;

        $this->assertInstanceOf( StockLevel::class , $item->inventoryLevel ) ;
        $this->assertSame( 120 , $item->inventoryLevel->value ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAServiceWhenTheTypeSaysSo(): void
    {
        $item = hydrateDocumentLineItem( [ '@type' => 'Service' , 'name' => 'Pose de sol' ] ) ;

        $this->assertInstanceOf( Service::class , $item ) ;
        $this->assertSame( 'Pose de sol' , $item->name ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAServiceSubTypeWhateverItsCase(): void
    {
        $this->assertInstanceOf( Service::class , hydrateDocumentLineItem( [ '@type' => 'FoodService' ] ) ) ;
        $this->assertInstanceOf( Service::class , hydrateDocumentLineItem( [ '@type' => 'service'     ] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testFallsBackToAProductOnAnUnrelatedOrInvalidType(): void
    {
        $this->assertInstanceOf( Product::class , hydrateDocumentLineItem( [ '@type' => 'Product' ] ) ) ;
        $this->assertInstanceOf( Product::class , hydrateDocumentLineItem( [ '@type' => 'Thing'   ] ) ) ;
        $this->assertInstanceOf( Product::class , hydrateDocumentLineItem( [ '@type' => 12345     ] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $items = hydrateDocumentLineItem(
        [
            [ 'name' => 'Peinture blanche' ] ,
            [ '@type' => 'Service' , 'name' => 'Pose de sol' ] ,
        ]) ;

        $this->assertIsArray( $items ) ;
        $this->assertCount( 2 , $items ) ;

        $this->assertInstanceOf( Product::class , $items[ 0 ] ) ;
        $this->assertInstanceOf( Service::class , $items[ 1 ] ) ;

        // A bare reference is kept — in a list as much as on its own. Only an entry that
        // resolved to nothing is dropped.
        $this->assertSame( [ 'item-ref-42' ] , hydrateDocumentLineItem( [ 'item-ref-42' ] ) ) ;
        $this->assertNull( hydrateDocumentLineItem( [ null ] ) ) ;
        $this->assertNull( hydrateDocumentLineItem( [] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAnAlreadyHydratedItem(): void
    {
        $product = new Product( [ 'name' => 'Peinture blanche' ] ) ;

        $this->assertSame( $product , hydrateDocumentLineItem( $product ) ) ;
        $this->assertSame( [ $product ] , hydrateDocumentLineItem( [ $product ] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateDocumentLineItem() ) ;
        $this->assertSame( 'raw' , hydrateDocumentLineItem( 'raw' ) ) ;
    }

    /**
     * 🔑 **A bare reference survives inside a list**, exactly as it does on its own — the
     * contract every helper of the family states in its header, applied entry by entry.
     * A property that stores handles rather than resolved objects used to read back `null`.
     *
     * The keys matter as much as the contents : a filtered list left with gaps serializes
     * as a JSON **object**, and a consumer walking the value gets something it cannot walk.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testAListOfReferencesSurvivesAndKeepsItsKeys(): void
    {
        $bare = hydrateDocumentLineItem( [ 'item-ref-42' , 'item-ref-42' ] ) ;

        $this->assertSame( [ 'item-ref-42' , 'item-ref-42' ] , $bare ) ;

        $mixed = hydrateDocumentLineItem( [ 'item-ref-42' , [ 'name' => 'Model A widget' ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 'item-ref-42' , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( Product::class , $mixed[ 1 ] ) ;
    }
}
