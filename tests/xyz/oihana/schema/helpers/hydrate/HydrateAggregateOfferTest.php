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
     * @throws ReflectionException
     */
    public function testReturnsNullOnNullInput(): void
    {
        $this->assertNull( hydrateAggregateOffer() ) ;
        $this->assertNull( hydrateAggregateOffer( null ) ) ;
    }
}
