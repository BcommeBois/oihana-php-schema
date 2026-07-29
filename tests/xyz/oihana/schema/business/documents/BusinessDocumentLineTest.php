<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\CompoundPriceSpecification;
use org\schema\MonetaryAmount;
use org\schema\QuantitativeValue;
use org\schema\StructuredValue;
use org\schema\UnitPriceSpecification;

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

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesPriceIntoCompoundPriceSpecification(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::PRICE =>
                [
                    'price'         => 20.0  ,
                    'priceCurrency' => 'EUR' ,
                ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertInstanceOf( CompoundPriceSpecification::class , $line->price ) ;
        $this->assertSame( 20.0  , $line->price->price         ) ;
        $this->assertSame( 'EUR' , $line->price->priceCurrency ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesThePriceComponents(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::PRICE =>
                [
                    'price'          => 22.5  ,
                    'priceCurrency'  => 'EUR' ,
                    'priceComponent' =>
                    [
                        [ 'name' => 'base'    , 'price' => 20.0 , 'priceCurrency' => 'EUR' ] ,
                        [ 'name' => 'ecoFee'  , 'price' => 2.5  , 'priceCurrency' => 'EUR' ] ,
                    ] ,
                ] ,
            ],
            BusinessDocumentLine::class
        );

        $this->assertInstanceOf( CompoundPriceSpecification::class , $line->price ) ;
        $this->assertIsArray( $line->price->priceComponent ) ;
        $this->assertCount( 2 , $line->price->priceComponent ) ;

        $this->assertInstanceOf( UnitPriceSpecification::class , $line->price->priceComponent[ 0 ] ) ;
        $this->assertSame( 'base' , $line->price->priceComponent[ 0 ]->name  ) ;
        $this->assertSame( 20.0   , $line->price->priceComponent[ 0 ]->price ) ;

        $this->assertInstanceOf( UnitPriceSpecification::class , $line->price->priceComponent[ 1 ] ) ;
        $this->assertSame( 'ecoFee' , $line->price->priceComponent[ 1 ]->name  ) ;
        $this->assertSame( 2.5      , $line->price->priceComponent[ 1 ]->price ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratedPriceKeepsItsTypeOnSerialization(): void
    {
        $line = new Reflection()->hydrate
        (
            [
                BusinessDocumentLine::PRICE => [ 'price' => 20.0 , 'priceCurrency' => 'EUR' ] ,
            ],
            BusinessDocumentLine::class
        );

        $json = json_decode( json_encode( $line ) , true ) ;

        $this->assertSame( 'CompoundPriceSpecification' , $json[ 'price' ][ '@type' ] ) ;
        $this->assertEquals( 20.0 , $json[ 'price' ][ 'price'         ] ) ;
        $this->assertSame( 'EUR'  , $json[ 'price' ][ 'priceCurrency' ] ) ;
    }
}
