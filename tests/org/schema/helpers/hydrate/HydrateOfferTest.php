<?php

namespace tests\org\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;
use org\schema\Offer;
use org\schema\PriceSpecification;
use org\schema\Product;
use org\schema\QuantitativeValue;
use org\schema\Service;

use xyz\oihana\schema\products\Product as BusinessProduct;

use function org\schema\helpers\hydrate\hydrateOffer;

final class HydrateOfferTest extends TestCase
{
    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesASingleOfferWithItsNestedReferences(): void
    {
        $offer = hydrateOffer
        ([
            'description'        => 'Worth showing at the next meeting.' ,
            'itemOffered'        => [ Schema::AT_TYPE => 'Product' , 'name' => 'Model A widget' ] ,
            'eligibleQuantity'   => [ 'value' => 10 , 'unitCode' => 'C62' ] ,
            'priceSpecification' => [ 'price' => 42.5 , 'priceCurrency' => 'EUR' ] ,
        ]) ;

        $this->assertInstanceOf( Offer::class             , $offer ) ;
        $this->assertInstanceOf( Product::class           , $offer->itemOffered ) ;
        $this->assertInstanceOf( QuantitativeValue::class , $offer->eligibleQuantity ) ;
        $this->assertInstanceOf( PriceSpecification::class, $offer->priceSpecification ) ;

        $this->assertSame( 'Model A widget' , $offer->itemOffered->name ) ;
        $this->assertSame( 10 , $offer->eligibleQuantity->value ) ;
    }

    /**
     * The `CreativeWork|Event|Product|Service` union cannot be settled by the property
     * type : the `@type` of the payload is what says which one was meant.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testReadsTheItemOfferedFromItsType(): void
    {
        $service = hydrateOffer( [ 'itemOffered' => [ Schema::AT_TYPE => 'Service' , 'name' => 'Yearly maintenance' ] ] ) ;
        $this->assertInstanceOf( Service::class , $service->itemOffered ) ;

        // Any Service subtype answers the same, its name ending with the word.
        $food = hydrateOffer( [ 'itemOffered' => [ Schema::AT_TYPE => 'FoodService' ] ] ) ;
        $this->assertInstanceOf( Service::class , $food->itemOffered ) ;

        // Nothing said : a product, the safe default of a commerce payload.
        $product = hydrateOffer( [ 'itemOffered' => [ 'name' => 'Model A widget' ] ] ) ;
        $this->assertInstanceOf( Product::class , $product->itemOffered ) ;
    }

    /**
     * `org\schema` knows nothing of `xyz\oihana` : the commerce-enriched product is a
     * parameter, not an import — which is what lets the business side keep its own
     * class without the layering being broken.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testTheProductClassIsAParameter(): void
    {
        $offer = hydrateOffer
        (
            [ 'itemOffered' => [ 'name' => 'Model A widget' ] ] ,
            BusinessProduct::class
        );

        $this->assertInstanceOf( BusinessProduct::class , $offer->itemOffered ) ;

        // A service payload is a service whatever the product class asked for.
        $service = hydrateOffer( [ 'itemOffered' => [ Schema::AT_TYPE => 'Service' ] ] , BusinessProduct::class ) ;
        $this->assertInstanceOf( Service::class , $service->itemOffered ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $offers = hydrateOffer
        ([
            [ 'itemOffered' => [ 'name' => 'Model A widget' ] ] ,
            [ 'itemOffered' => [ 'name' => 'Model B widget' ] ] ,
        ]) ;

        $this->assertIsArray( $offers ) ;
        $this->assertCount( 2 , $offers ) ;
        $this->assertContainsOnlyInstancesOf( Offer::class , $offers ) ;

        $this->assertNull( hydrateOffer( [] ) ) ;
    }

    /**
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateOffer() ) ;
        $this->assertSame( 'offer-ref-42' , hydrateOffer( 'offer-ref-42' ) ) ;

        $offer = new Offer() ;
        $this->assertSame( $offer , hydrateOffer( $offer ) ) ;
    }

    /**
     * An offer carries one item ; a list of them is still read as a list, and one that
     * resolves to nothing answers `null` rather than a leftover raw array.
     *
     * @throws HydrationException
     * @throws ReflectionException
     */
    public function testAListOfItemsIsReadAsAList(): void
    {
        $offer = hydrateOffer
        ([
            'itemOffered' =>
            [
                [ 'name' => 'Model A widget' ] ,
                [ Schema::AT_TYPE => 'Service' , 'name' => 'Yearly maintenance' ] ,
            ]
        ]) ;

        $this->assertIsArray( $offer->itemOffered ) ;
        $this->assertCount( 2 , $offer->itemOffered ) ;
        $this->assertInstanceOf( Product::class , $offer->itemOffered[ 0 ] ) ;
        $this->assertInstanceOf( Service::class , $offer->itemOffered[ 1 ] ) ;

        $this->assertNull( hydrateOffer( [ 'itemOffered' => [] ] )->itemOffered ) ;
    }
}
