<?php

namespace tests\org\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\enumerations\BusinessEntityType;
use org\schema\OfferForPurchase;

use function org\schema\helpers\hydrate\hydrateOfferPurchase;

final class HydrateOfferPurchaseTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesAnArrayDefinition(): void
    {
        $offer = hydrateOfferPurchase( [ 'price' => 12.5 ] ) ;

        $this->assertInstanceOf( OfferForPurchase::class , $offer ) ;
        $this->assertSame( 12.5 , $offer->price ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesTheEligibleCustomerType(): void
    {
        $offer = hydrateOfferPurchase(
        [
            'price'                => 12.5 ,
            'eligibleCustomerType' => [ 'name' => 'Professional' ] ,
        ]) ;

        $this->assertInstanceOf( BusinessEntityType::class , $offer->eligibleCustomerType ) ;
        $this->assertSame( 'Professional' , $offer->eligibleCustomerType->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsAnExistingInstanceUnchanged(): void
    {
        $offer = new OfferForPurchase( [ 'price' => 8 ] ) ;
        $this->assertSame( $offer , hydrateOfferPurchase( $offer ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsNullOnAnUnsupportedInput(): void
    {
        $this->assertNull( hydrateOfferPurchase( 'raw' ) ) ;
        $this->assertNull( hydrateOfferPurchase( null ) ) ;
    }
}
