<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;
use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\PriceComponentType;
use xyz\oihana\schema\business\documents\TaxDetail;

class AdjustmentTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new Adjustment() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , Adjustment::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'amount'         , Adjustment::AMOUNT           );
        $this->assertSame( 'includedInBase' , Adjustment::INCLUDED_IN_BASE );
        $this->assertSame( 'percentage'     , Adjustment::PERCENTAGE       );
        $this->assertSame( 'reason'         , Adjustment::REASON           );
        $this->assertSame( 'taxes'          , Adjustment::TAXES            );
        $this->assertSame( 'type'           , Adjustment::TYPE             );

        $this->assertSame( Oihana::AMOUNT , Adjustment::AMOUNT );
        $this->assertSame( Oihana::TYPE   , Adjustment::TYPE   );
    }

    public function testDefaults(): void
    {
        $adjustment = new Adjustment() ;

        $this->assertNull( $adjustment->amount         ?? null );
        $this->assertNull( $adjustment->includedInBase ?? null );
        $this->assertNull( $adjustment->percentage     ?? null );
        $this->assertNull( $adjustment->reason         ?? null );
        $this->assertNull( $adjustment->taxes          ?? null );
        $this->assertNull( $adjustment->type           ?? null );
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testConstructorHydratesScalarProperties(): void
    {
        $adjustment = new Adjustment
        ([
            Adjustment::TYPE             => PriceComponentType::DISCOUNT ,
            Adjustment::PERCENTAGE       => 10.0 ,
            Adjustment::REASON           => 'Loyalty discount' ,
            Adjustment::INCLUDED_IN_BASE => false ,
        ]);

        $this->assertSame( PriceComponentType::DISCOUNT , $adjustment->type ) ;
        $this->assertSame( 10.0                          , $adjustment->percentage ) ;
        $this->assertSame( 'Loyalty discount'             , $adjustment->reason ) ;
        $this->assertFalse( $adjustment->includedInBase ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesAmountIntoMonetaryAmount(): void
    {
        $adjustment = new Reflection()->hydrate
        (
            [ Adjustment::AMOUNT => [ 'value' => 15 , 'currency' => 'EUR' ] ],
            Adjustment::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $adjustment->amount ) ;
        $this->assertSame( 15 , $adjustment->amount->value ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTaxesIntoTaxDetails(): void
    {
        $adjustment = new Reflection()->hydrate
        (
            [
                Adjustment::AMOUNT => [ 'value' => 52 , 'currency' => 'EUR' ] ,
                Adjustment::TAXES  =>
                    [
                        [
                            'basisAmount' => [ 'value' => 52   , 'currency' => 'EUR' ] ,
                            'rate'        => 20 ,
                            'taxAmount'   => [ 'value' => 10.4 , 'currency' => 'EUR' ] ,
                        ],
                    ],
            ],
            Adjustment::class
        );

        $this->assertIsArray( $adjustment->taxes ) ;
        $this->assertCount( 1 , $adjustment->taxes ) ;
        $this->assertInstanceOf( TaxDetail::class      , $adjustment->taxes[ 0 ] ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $adjustment->taxes[ 0 ]->taxAmount ) ;
        $this->assertSame( 20   , $adjustment->taxes[ 0 ]->rate ) ;
        $this->assertSame( 10.4 , $adjustment->taxes[ 0 ]->taxAmount->value ) ;
    }
}
