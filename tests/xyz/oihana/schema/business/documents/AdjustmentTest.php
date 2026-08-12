<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;
use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\BusinessDocumentLine;
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
        $this->assertSame( 'amount'          , Adjustment::AMOUNT            );
        $this->assertSame( 'includedInBase'  , Adjustment::INCLUDED_IN_BASE  );
        $this->assertSame( 'includedInTotal' , Adjustment::INCLUDED_IN_TOTAL );
        $this->assertSame( 'percentage'      , Adjustment::PERCENTAGE        );
        $this->assertSame( 'reason'          , Adjustment::REASON            );
        $this->assertSame( 'taxes'           , Adjustment::TAXES             );
        $this->assertSame( 'type'            , Adjustment::TYPE              );

        $this->assertSame( Oihana::AMOUNT , Adjustment::AMOUNT );
        $this->assertSame( Oihana::TYPE   , Adjustment::TYPE   );
    }

    public function testDefaults(): void
    {
        $adjustment = new Adjustment() ;

        $this->assertNull( $adjustment->amount          ?? null );
        $this->assertNull( $adjustment->includedInBase  ?? null );
        $this->assertNull( $adjustment->includedInTotal ?? null );
        $this->assertNull( $adjustment->percentage      ?? null );
        $this->assertNull( $adjustment->reason          ?? null );
        $this->assertNull( $adjustment->taxes           ?? null );
        $this->assertNull( $adjustment->type            ?? null );
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

    /**
     * An adjustment that says nothing about it counts : the property only ever
     * states the exception, so nothing stored before it existed changes
     * meaning.
     */
    public function testAnAdjustmentCountsUnlessItSaysOtherwise(): void
    {
        $counted = new Adjustment([ Adjustment::TYPE => PriceComponentType::ENVIRONMENTAL_FEE ]) ;
        $this->assertNull( $counted->includedInTotal ) ;

        $left = new Adjustment
        ([
            Adjustment::TYPE              => PriceComponentType::ENVIRONMENTAL_FEE ,
            Adjustment::INCLUDED_IN_TOTAL => false ,
        ]);

        $this->assertFalse( $left->includedInTotal ) ;
    }

    /**
     * The two flags answer different questions and must not collapse into one :
     * `includedInBase` says the amount is already inside the base price,
     * `includedInTotal` says whether it reaches the totals at all. An
     * adjustment can be added on top of the price *and* left out of the total.
     */
    public function testIncludedInTotalIsNotIncludedInBase(): void
    {
        $adjustment = new Adjustment
        ([
            Adjustment::INCLUDED_IN_BASE  => false ,
            Adjustment::INCLUDED_IN_TOTAL => false ,
        ]);

        $this->assertFalse( $adjustment->includedInBase  ) ;
        $this->assertFalse( $adjustment->includedInTotal ) ;
    }

    /**
     * The same name, the same default and the same meaning as the one already
     * carried by a document line : a consumer learns the rule once.
     */
    public function testItReadsExactlyLikeTheFlagOfADocumentLine(): void
    {
        $this->assertSame( BusinessDocumentLine::INCLUDED_IN_TOTAL , Adjustment::INCLUDED_IN_TOTAL ) ;

        $this->assertNull( new Adjustment()->includedInTotal ) ;
        $this->assertNull( new BusinessDocumentLine()->includedInTotal ) ;
    }
}
