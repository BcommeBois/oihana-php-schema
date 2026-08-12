<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\AggregateOffer;
use org\schema\OfferForPurchase;
use org\schema\QuantitativeValue;

use xyz\oihana\schema\organizations\Provider;
use xyz\oihana\schema\organizations\Subsidiary;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\products\PhysicalQuantity;

use function xyz\oihana\schema\helpers\hydrate\hydrateAggregateOffer;

final class HydrateAggregateOfferTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesTheNestedReferences(): void
    {
        $offer = hydrateAggregateOffer(
        [
            'availableAtOrFrom' => [ 'name' => 'Bayonne' , 'ownedBy' => [ 'name' => 'South Branch' ] ] ,
            'eligibleQuantity'  => [ 'value' => 1 , 'unitCode' => 'C62' ] ,
            'offers'            => [ [ 'price' => 12.5 ] , [ 'price' => 11.9 ] ] ,
            'provider'          => [ 'name' => 'ACME' ] ,
        ]) ;

        $this->assertInstanceOf( AggregateOffer::class , $offer ) ;
        $this->assertInstanceOf( Warehouse::class         , $offer->availableAtOrFrom ) ;
        $this->assertInstanceOf( Subsidiary::class        , $offer->availableAtOrFrom->ownedBy ) ;
        $this->assertInstanceOf( QuantitativeValue::class , $offer->eligibleQuantity ) ;
        $this->assertInstanceOf( Provider::class          , $offer->provider ) ;

        $this->assertIsArray( $offer->offers ) ;
        $this->assertCount( 2 , $offer->offers ) ;
        $this->assertContainsOnlyInstancesOf( OfferForPurchase::class , $offer->offers ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testKeepsAMinimalOfferUntouched(): void
    {
        $offer = hydrateAggregateOffer( [ 'lowPrice' => 9.9 ] ) ;

        $this->assertInstanceOf( AggregateOffer::class , $offer ) ;
        $this->assertSame( 9.9 , $offer->lowPrice ) ;
    }

    /**
     * The unit of a packaging level keeps what that level weighs. A plain
     * `QuantitativeValue` declares no `weight`, and a class discards the keys
     * it does not declare : the measure would leave without an error and
     * without a trace in the payload.
     *
     * @throws ReflectionException
     */
    public function testTheEligibleQuantityKeepsWhatTheLevelWeighs(): void
    {
        $offer = hydrateAggregateOffer(
        [
            'eligibleQuantity' =>
            [
                'value'    => 1.403 ,
                'unitCode' => 'PK'  ,
                'weight'   => 15.419 ,
                'volume'   => 0.0312 ,
            ] ,
        ]) ;

        $this->assertInstanceOf( PhysicalQuantity::class , $offer->eligibleQuantity ) ;
        $this->assertSame( 1.403  , $offer->eligibleQuantity->value  ) ;
        $this->assertSame( 15.419 , $offer->eligibleQuantity->weight ) ;
        $this->assertSame( 0.0312 , $offer->eligibleQuantity->volume ) ;
    }

    /**
     * A level that states nothing about its mass hydrates exactly as it always
     * did, and stays readable through the mirror type.
     *
     * @throws ReflectionException
     */
    public function testTheEligibleQuantityWithoutAnyMeasureIsUnchanged(): void
    {
        $offer = hydrateAggregateOffer( [ 'eligibleQuantity' => [ 'value' => 1 , 'unitCode' => 'C62' ] ] ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $offer->eligibleQuantity ) ;
        $this->assertSame( 1     , $offer->eligibleQuantity->value    ) ;
        $this->assertSame( 'C62' , $offer->eligibleQuantity->unitCode ) ;
        $this->assertNull( $offer->eligibleQuantity->weight ?? null ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsNullOnNullInput(): void
    {
        $this->assertNull( hydrateAggregateOffer() ) ;
        $this->assertNull( hydrateAggregateOffer( null ) ) ;
    }
}
