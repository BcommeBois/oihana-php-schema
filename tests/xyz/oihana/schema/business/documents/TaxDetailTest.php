<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\constants\Oihana;

class TaxDetailTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new TaxDetail() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , TaxDetail::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'basisAmount' , TaxDetail::BASIS_AMOUNT );
        $this->assertSame( 'category'    , TaxDetail::CATEGORY     );
        $this->assertSame( 'rate'        , TaxDetail::RATE         );
        $this->assertSame( 'taxAmount'   , TaxDetail::TAX_AMOUNT   );

        $this->assertSame( Oihana::BASIS_AMOUNT , TaxDetail::BASIS_AMOUNT );
        $this->assertSame( Oihana::TAX_AMOUNT   , TaxDetail::TAX_AMOUNT   );
    }

    public function testDefaults(): void
    {
        $tax = new TaxDetail() ;

        $this->assertNull( $tax->basisAmount ?? null );
        $this->assertNull( $tax->category    ?? null );
        $this->assertNull( $tax->rate        ?? null );
        $this->assertNull( $tax->taxAmount   ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $tax = new TaxDetail
        ([
            TaxDetail::CATEGORY => 'Standard VAT rate' ,
            TaxDetail::RATE     => 20.0 ,
        ]);

        $this->assertSame( 'Standard VAT rate' , $tax->category ) ;
        $this->assertSame( 20.0                , $tax->rate     ) ;
    }

    public function testConstructorKeepsAmountsAsRawArrays(): void
    {
        $tax = new TaxDetail
        ([
            TaxDetail::BASIS_AMOUNT => [ 'value' => 100 , 'currency' => 'EUR' ] ,
        ]);

        $this->assertIsArray( $tax->basisAmount ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesAmountsIntoMonetaryAmount(): void
    {
        $tax = new Reflection()->hydrate
        (
            [
                TaxDetail::BASIS_AMOUNT => [ 'value' => 100 , 'currency' => 'EUR' ] ,
                TaxDetail::TAX_AMOUNT   => [ 'value' => 20  , 'currency' => 'EUR' ] ,
            ],
            TaxDetail::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $tax->basisAmount ) ;
        $this->assertSame( 100 , $tax->basisAmount->value ) ;

        $this->assertInstanceOf( MonetaryAmount::class , $tax->taxAmount ) ;
        $this->assertSame( 20 , $tax->taxAmount->value ) ;
    }
}
