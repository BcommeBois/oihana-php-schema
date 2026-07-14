<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\OfferForPurchase;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\PricingTargetScope;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\products\CustomerOffer;
use xyz\oihana\schema\products\PricingCondition;

class CustomerOfferTest extends TestCase
{
    public function testIsOfferForPurchase(): void
    {
        $this->assertInstanceOf( OfferForPurchase::class , new CustomerOffer() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , CustomerOffer::CONTEXT );
    }

    public function testConstantsAreWiredOnTheAggregator(): void
    {
        $this->assertSame( 'appliedCondition' , Oihana::APPLIED_CONDITION );
        $this->assertSame( 'customer'         , Oihana::CUSTOMER          );
    }

    public function testDefaults(): void
    {
        $offer = new CustomerOffer() ;

        $this->assertNull( $offer->appliedCondition ?? null );
        $this->assertNull( $offer->customer         ?? null );
    }

    public function testConstructorHydratesTheInheritedPricingSurface(): void
    {
        $offer = new CustomerOffer
        ([
            Oihana::PRICE          => 9.20 ,
            Oihana::PRICE_CURRENCY => 'EUR' ,
        ]);

        $this->assertSame( 9.20  , $offer->price         ) ;
        $this->assertSame( 'EUR' , $offer->priceCurrency ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheCustomerReference(): void
    {
        $offer = new Reflection()->hydrate
        (
            [
                Oihana::CUSTOMER =>
                [
                    Oihana::ID   => '216303' ,
                    Oihana::NAME => 'Menuiserie Fabre' ,
                ] ,
            ],
            CustomerOffer::class
        );

        $this->assertInstanceOf( Customer::class , $offer->customer ) ;
        $this->assertSame( '216303'           , $offer->customer->id   ) ;
        $this->assertSame( 'Menuiserie Fabre' , $offer->customer->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheAppliedCondition(): void
    {
        $offer = new Reflection()->hydrate
        (
            [
                Oihana::APPLIED_CONDITION =>
                [
                    Oihana::SELECTOR =>
                    [
                        Oihana::CUSTOMER_SCOPE => PricingTargetScope::INDIVIDUAL ,
                        Oihana::CUSTOMER_ID    => '216303' ,
                    ] ,
                ] ,
            ],
            CustomerOffer::class
        );

        $this->assertInstanceOf( PricingCondition::class , $offer->appliedCondition ) ;
        $this->assertSame( '216303' , $offer->appliedCondition->selector->customerId ) ;
    }
}
