<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\DocumentTotals;
use xyz\oihana\schema\constants\Oihana;

class DocumentTotalsTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new DocumentTotals() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , DocumentTotals::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'allowanceTotal' , DocumentTotals::ALLOWANCE_TOTAL );
        $this->assertSame( 'balanceDue'     , DocumentTotals::BALANCE_DUE     );
        $this->assertSame( 'chargeTotal'    , DocumentTotals::CHARGE_TOTAL    );
        $this->assertSame( 'prepaidAmount'  , DocumentTotals::PREPAID_AMOUNT  );
        $this->assertSame( 'subtotal'       , DocumentTotals::SUBTOTAL        );
        $this->assertSame( 'total'          , DocumentTotals::TOTAL           );
        $this->assertSame( 'totalTax'       , DocumentTotals::TOTAL_TAX       );

        $this->assertSame( Oihana::ALLOWANCE_TOTAL , DocumentTotals::ALLOWANCE_TOTAL );
        $this->assertSame( Oihana::BALANCE_DUE     , DocumentTotals::BALANCE_DUE     );
        $this->assertSame( Oihana::CHARGE_TOTAL    , DocumentTotals::CHARGE_TOTAL    );
    }

    public function testDefaults(): void
    {
        $totals = new DocumentTotals() ;

        $this->assertNull( $totals->allowanceTotal ?? null );
        $this->assertNull( $totals->balanceDue     ?? null );
        $this->assertNull( $totals->chargeTotal    ?? null );
        $this->assertNull( $totals->prepaidAmount  ?? null );
        $this->assertNull( $totals->subtotal       ?? null );
        $this->assertNull( $totals->total          ?? null );
        $this->assertNull( $totals->totalTax       ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesEveryAmountIntoMonetaryAmount(): void
    {
        $totals = new Reflection()->hydrate
        (
            [
                DocumentTotals::SUBTOTAL        => [ 'value' => 100 , 'currency' => 'EUR' ] ,
                DocumentTotals::TOTAL_TAX       => [ 'value' => 20  , 'currency' => 'EUR' ] ,
                DocumentTotals::TOTAL           => [ 'value' => 120 , 'currency' => 'EUR' ] ,
                DocumentTotals::PREPAID_AMOUNT  => [ 'value' => 50  , 'currency' => 'EUR' ] ,
                DocumentTotals::BALANCE_DUE     => [ 'value' => 70  , 'currency' => 'EUR' ] ,
                DocumentTotals::ALLOWANCE_TOTAL => [ 'value' => 15  , 'currency' => 'EUR' ] ,
                DocumentTotals::CHARGE_TOTAL    => [ 'value' => 8   , 'currency' => 'EUR' ] ,
            ],
            DocumentTotals::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $totals->subtotal ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $totals->totalTax ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $totals->total ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $totals->prepaidAmount ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $totals->balanceDue ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $totals->allowanceTotal ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $totals->chargeTotal ) ;

        $this->assertSame( 100 , $totals->subtotal->value ) ;
        $this->assertSame( 70  , $totals->balanceDue->value ) ;
        $this->assertSame( 15  , $totals->allowanceTotal->value ) ;
        $this->assertSame( 8   , $totals->chargeTotal->value ) ;
    }

    public function testConstructorKeepsAmountsAsRawArrays(): void
    {
        $totals = new DocumentTotals
        ([
            DocumentTotals::TOTAL => [ 'value' => 120 , 'currency' => 'EUR' ] ,
        ]);

        $this->assertIsArray( $totals->total ) ;
    }
}
