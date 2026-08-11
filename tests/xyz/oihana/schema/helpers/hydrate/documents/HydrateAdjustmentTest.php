<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\MonetaryAmount;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\enumerations\PriceComponentType;

use function xyz\oihana\schema\helpers\hydrate\documents\hydrateAdjustment;

final class HydrateAdjustmentTest extends TestCase
{
    /**
     * The resolution goes two levels down on a single call : the adjustment's own
     * amount, and the amounts each tax detail declares in its turn.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesASingleAdjustmentWithItsAmountAndTaxes(): void
    {
        $adjustment = hydrateAdjustment(
        [
            'type'           => PriceComponentType::SHIPPING_FEE ,
            'reason'         => 'Franco 1000€ < frais 52€' ,
            'includedInBase' => false ,
            'amount'         => [ 'value' => 52.0 , 'currency' => 'EUR' ] ,
            'taxes'          =>
            [
                [
                    'basisAmount' => [ 'value' => 52.0 , 'currency' => 'EUR' ] ,
                    'rate'        => 20.0 ,
                    'taxAmount'   => [ 'value' => 10.4 , 'currency' => 'EUR' ] ,
                ] ,
            ] ,
        ]) ;

        $this->assertInstanceOf( Adjustment::class , $adjustment ) ;

        $this->assertSame( PriceComponentType::SHIPPING_FEE , $adjustment->type   ) ;
        $this->assertSame( 'Franco 1000€ < frais 52€'       , $adjustment->reason ) ;
        $this->assertFalse( $adjustment->includedInBase ) ;

        $this->assertInstanceOf( MonetaryAmount::class , $adjustment->amount ) ;
        $this->assertSame( 52.0  , $adjustment->amount->value    ) ;
        $this->assertSame( 'EUR' , $adjustment->amount->currency ) ;

        $this->assertContainsOnlyInstancesOf( TaxDetail::class , $adjustment->taxes ) ;
        $this->assertSame( 20.0 , $adjustment->taxes[ 0 ]->rate ) ;

        $this->assertInstanceOf( MonetaryAmount::class , $adjustment->taxes[ 0 ]->basisAmount ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $adjustment->taxes[ 0 ]->taxAmount   ) ;
        $this->assertSame( 10.4 , $adjustment->taxes[ 0 ]->taxAmount->value ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAMinimalAdjustmentUntouched(): void
    {
        $adjustment = hydrateAdjustment( [ 'percentage' => 7.15 ] ) ;

        $this->assertInstanceOf( Adjustment::class , $adjustment ) ;
        $this->assertSame( 7.15 , $adjustment->percentage ) ;
        $this->assertNull( $adjustment->amount ?? null ) ;
    }

    /**
     * The list is the usual shape : a document commonly carries a carriage charge
     * beside an environmental fee.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $adjustments = hydrateAdjustment(
        [
            [ 'type' => PriceComponentType::SHIPPING_FEE , 'amount' => [ 'value' => 52.0 , 'currency' => 'EUR' ] ] ,
            [ 'reason' => 'Eco-participation' , 'amount' => [ 'value' => 1.2 , 'currency' => 'EUR' ] ] ,
        ]) ;

        $this->assertIsArray( $adjustments ) ;
        $this->assertCount( 2 , $adjustments ) ;
        $this->assertContainsOnlyInstancesOf( Adjustment::class , $adjustments ) ;

        $this->assertInstanceOf( MonetaryAmount::class , $adjustments[ 0 ]->amount ) ;
        $this->assertSame( 1.2 , $adjustments[ 1 ]->amount->value ) ;

        $this->assertNull( hydrateAdjustment( [ 'raw' ] ) ) ;
    }

    /**
     * 🔑 « No adjustment » is an answer, and it is not the answer « nothing here was
     * readable ». The empty list survives so a consumer can map over it; a non-empty
     * list that hydrates to nothing keeps the family's `null`.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testAnEmptyListStaysAnEmptyList(): void
    {
        $this->assertSame( [] , hydrateAdjustment( [] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testKeepsAlreadyHydratedAdjustments(): void
    {
        $adjustment = new Adjustment( [ 'reason' => 'Remise' ] ) ;

        $this->assertSame( $adjustment , hydrateAdjustment( $adjustment ) ) ;
        $this->assertSame( [ $adjustment ] , hydrateAdjustment( [ $adjustment ] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateAdjustment() ) ;
        $this->assertSame( 'raw' , hydrateAdjustment( 'raw' ) ) ;
    }
}
