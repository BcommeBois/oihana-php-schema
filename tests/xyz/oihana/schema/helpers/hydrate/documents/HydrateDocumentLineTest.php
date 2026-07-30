<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\CompoundPriceSpecification;
use org\schema\MonetaryAmount;
use org\schema\QuantitativeValue;
use org\schema\Service;
use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\BusinessDocumentLine;
use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\products\Product;

use function xyz\oihana\schema\helpers\hydrate\documents\hydrateDocumentLine;

final class HydrateDocumentLineTest extends TestCase
{
    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesASingleLineWithItsNestedReferences(): void
    {
        $line = hydrateDocumentLine(
        [
            'position'    => 1 ,
            'quantity'    => [ 'value' => 5 , 'unitCode' => 'C62' ] ,
            'price'       =>
            [
                'price'          => 22.5  ,
                'priceCurrency'  => 'EUR' ,
                'priceComponent' =>
                [
                    [ 'name' => 'base'   , 'price' => 20.0 , 'priceCurrency' => 'EUR' ] ,
                    [ 'name' => 'ecoFee' , 'price' => 2.5  , 'priceCurrency' => 'EUR' ] ,
                ] ,
            ] ,
            'subtotal'    => [ 'value' => 112.5 , 'currency' => 'EUR' ] ,
            'total'       => [ 'value' => 135.0 , 'currency' => 'EUR' ] ,
            'taxes'       => [ [ 'category' => 'VAT' , 'rate' => 20.0 , 'taxAmount' => [ 'value' => 22.5 , 'currency' => 'EUR' ] ] ] ,
            'adjustments' => [ [ 'type' => 'discount' , 'amount' => [ 'value' => 5 , 'currency' => 'EUR' ] ] ] ,
        ]) ;

        $this->assertInstanceOf( BusinessDocumentLine::class , $line ) ;
        $this->assertSame( 1 , $line->position ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $line->quantity ) ;
        $this->assertSame( 5 , $line->quantity->value ) ;

        $this->assertInstanceOf( CompoundPriceSpecification::class , $line->price ) ;
        $this->assertSame( 22.5 , $line->price->price ) ;
        $this->assertContainsOnlyInstancesOf( UnitPriceSpecification::class , $line->price->priceComponent ) ;
        $this->assertCount( 2 , $line->price->priceComponent ) ;

        $this->assertInstanceOf( MonetaryAmount::class , $line->subtotal ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $line->total    ) ;

        $this->assertContainsOnlyInstancesOf( TaxDetail::class , $line->taxes ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $line->taxes[ 0 ]->taxAmount ) ;

        $this->assertContainsOnlyInstancesOf( Adjustment::class , $line->adjustments ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $line->adjustments[ 0 ]->amount ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesTheItemAsAProductByDefault(): void
    {
        $line = hydrateDocumentLine(
        [
            'item' =>
            [
                'name'             => 'Peinture blanche' ,
                'eligibleQuantity' => [ 'value' => 12 , 'unitCode' => 'C62' ] ,
            ] ,
        ]) ;

        $this->assertInstanceOf( Product::class , $line->item ) ;
        $this->assertSame( 'Peinture blanche' , $line->item->name ) ;
        $this->assertInstanceOf( QuantitativeValue::class , $line->item->eligibleQuantity ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesTheItemAsAServiceWhenTheTypeSaysSo(): void
    {
        $line = hydrateDocumentLine(
        [
            'item' => [ '@type' => 'Service' , 'name' => 'Pose de sol' ] ,
        ]) ;

        $this->assertInstanceOf( Service::class , $line->item ) ;
        $this->assertSame( 'Pose de sol' , $line->item->name ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAMinimalLineUntouched(): void
    {
        $line = hydrateDocumentLine( [ 'position' => 3 ] ) ;

        $this->assertInstanceOf( BusinessDocumentLine::class , $line ) ;
        $this->assertSame( 3 , $line->position ) ;
        $this->assertNull( $line->item ?? null ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $lines = hydrateDocumentLine(
        [
            [ 'position' => 1 , 'quantity' => [ 'value' => 2 , 'unitCode' => 'C62' ] ] ,
            [ 'position' => 2 , 'total'    => [ 'value' => 10 , 'currency' => 'EUR' ] ] ,
        ]) ;

        $this->assertIsArray( $lines ) ;
        $this->assertCount( 2 , $lines ) ;
        $this->assertContainsOnlyInstancesOf( BusinessDocumentLine::class , $lines ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $lines[ 0 ]->quantity ) ;
        $this->assertInstanceOf( MonetaryAmount::class    , $lines[ 1 ]->total    ) ;

        $this->assertNull( hydrateDocumentLine( [ 'raw' ] ) ) ;
        $this->assertNull( hydrateDocumentLine( [] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAlreadyHydratedLines(): void
    {
        $line = new BusinessDocumentLine( [ 'position' => 1 ] ) ;

        $this->assertSame( $line , hydrateDocumentLine( $line ) ) ;

        $lines = hydrateDocumentLine( [ $line ] ) ;

        $this->assertSame( [ $line ] , $lines ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateDocumentLine() ) ;
        $this->assertSame( 'raw' , hydrateDocumentLine( 'raw' ) ) ;
    }
}
