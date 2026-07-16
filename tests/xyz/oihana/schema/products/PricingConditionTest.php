<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;
use org\schema\PropertyValue;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\PriceComponentType;
use xyz\oihana\schema\enumerations\PricingItemScope;
use xyz\oihana\schema\enumerations\PricingTargetScope;
use org\schema\DefinedTerm;

use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\organizations\Provider;
use xyz\oihana\schema\organizations\Subsidiary;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\products\PriceSegmentation;
use xyz\oihana\schema\products\PricingCondition;
use xyz\oihana\schema\products\PricingConditionSelector;
use xyz\oihana\schema\products\Product;

class PricingConditionTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new PricingCondition() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , PricingCondition::CONTEXT );
    }

    public function testConstantsAreWiredOnTheAggregator(): void
    {
        $this->assertSame( 'additionalProperty'  , Oihana::ADDITIONAL_PROPERTY );
        $this->assertSame( 'adjustment'          , Oihana::ADJUSTMENT          );
        $this->assertSame( 'category'            , Oihana::CATEGORY            );
        $this->assertSame( 'customer'            , Oihana::CUSTOMER            );
        $this->assertSame( 'excludedCustomers'   , Oihana::EXCLUDED_CUSTOMERS  );
        $this->assertSame( 'excludedProducts'    , Oihana::EXCLUDED_PRODUCTS   );
        $this->assertSame( 'fixedPrice'          , Oihana::FIXED_PRICE         );
        $this->assertSame( 'free'                , Oihana::FREE                );
        $this->assertSame( 'product'             , Oihana::PRODUCT             );
        $this->assertSame( 'provider'            , Oihana::PROVIDER            );
        $this->assertSame( 'quantityDiscount'    , Oihana::QUANTITY_DISCOUNT   );
        $this->assertSame( 'selector'            , Oihana::SELECTOR            );
        $this->assertSame( 'subsidiary'          , Oihana::SUBSIDIARY          );
        $this->assertSame( 'substitutesSegment'  , Oihana::SUBSTITUTES_SEGMENT );
        $this->assertSame( 'validFrom'           , Oihana::VALID_FROM          );
        $this->assertSame( 'validThrough'        , Oihana::VALID_THROUGH       );
        $this->assertSame( 'warehouse'           , Oihana::WAREHOUSE           );
    }

    public function testDefaults(): void
    {
        $condition = new PricingCondition() ;

        $this->assertNull( $condition->additionalProperty ?? null );
        $this->assertNull( $condition->adjustment         ?? null );
        $this->assertNull( $condition->category           ?? null );
        $this->assertNull( $condition->customer           ?? null );
        $this->assertNull( $condition->excludedCustomers  ?? null );
        $this->assertNull( $condition->excludedProducts   ?? null );
        $this->assertNull( $condition->fixedPrice         ?? null );
        $this->assertNull( $condition->free               ?? null );
        $this->assertNull( $condition->product            ?? null );
        $this->assertNull( $condition->provider           ?? null );
        $this->assertNull( $condition->quantityDiscount   ?? null );
        $this->assertNull( $condition->selector           ?? null );
        $this->assertNull( $condition->subsidiary         ?? null );
        $this->assertNull( $condition->substitutesSegment ?? null );
        $this->assertNull( $condition->validFrom          ?? null );
        $this->assertNull( $condition->validThrough       ?? null );
        $this->assertNull( $condition->warehouse          ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $condition = new PricingCondition
        ([
            Oihana::VALID_FROM         => '2026-01-01' ,
            Oihana::VALID_THROUGH      => '2026-12-31' ,
            Oihana::EXCLUDED_CUSTOMERS => [ '600160' ] ,
            Oihana::EXCLUDED_PRODUCTS  => [ '70196' ] ,
        ]);

        $this->assertSame( '2026-01-01' , $condition->validFrom         ) ;
        $this->assertSame( '2026-12-31' , $condition->validThrough      ) ;
        $this->assertSame( [ '600160' ] , $condition->excludedCustomers ) ;
        $this->assertSame( [ '70196' ]  , $condition->excludedProducts  ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheSelector(): void
    {
        $condition = new Reflection()->hydrate
        (
            [
                Oihana::SELECTOR =>
                [
                    Oihana::CUSTOMER_SCOPE => PricingTargetScope::GROUP ,
                    Oihana::CUSTOMER_ID    => '600214' ,
                    Oihana::ITEM_SCOPE     => PricingItemScope::CATEGORY ,
                    Oihana::ITEM_ID        => '05' ,
                ] ,
            ],
            PricingCondition::class
        );

        $this->assertInstanceOf( PricingConditionSelector::class , $condition->selector ) ;
        $this->assertSame( PricingTargetScope::GROUP  , $condition->selector->customerScope ) ;
        $this->assertSame( '600214'                   , $condition->selector->customerId    ) ;
        $this->assertSame( PricingItemScope::CATEGORY , $condition->selector->itemScope     ) ;
        $this->assertSame( '05'                       , $condition->selector->itemId        ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheAdjustmentEffect(): void
    {
        $condition = new Reflection()->hydrate
        (
            [
                Oihana::ADJUSTMENT =>
                [
                    [
                        Oihana::TYPE       => PriceComponentType::DISCOUNT ,
                        Oihana::PERCENTAGE => 10 ,
                    ] ,
                ] ,
            ],
            PricingCondition::class
        );

        $this->assertIsArray( $condition->adjustment ) ;
        $this->assertCount( 1 , $condition->adjustment ) ;
        $this->assertContainsOnlyInstancesOf( Adjustment::class , $condition->adjustment ) ;
        $this->assertSame( PriceComponentType::DISCOUNT , $condition->adjustment[0]->type ) ;
        $this->assertSame( 10 , $condition->adjustment[0]->percentage ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheStackedAdjustmentEffect(): void
    {
        $condition = new Reflection()->hydrate
        (
            [
                Oihana::ADJUSTMENT =>
                [
                    [
                        Oihana::TYPE       => PriceComponentType::DISCOUNT ,
                        Oihana::PERCENTAGE => 10 ,
                    ] ,
                    [
                        Oihana::TYPE       => PriceComponentType::DISCOUNT ,
                        Oihana::PERCENTAGE => 5 ,
                    ] ,
                ] ,
            ],
            PricingCondition::class
        );

        $this->assertIsArray( $condition->adjustment ) ;
        $this->assertCount( 2 , $condition->adjustment ) ;
        $this->assertContainsOnlyInstancesOf( Adjustment::class , $condition->adjustment ) ;
        $this->assertSame( 10 , $condition->adjustment[0]->percentage ) ;
        $this->assertSame( 5  , $condition->adjustment[1]->percentage ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheSubstitutionEffect(): void
    {
        $condition = new Reflection()->hydrate
        (
            [
                Oihana::SUBSTITUTES_SEGMENT =>
                [
                    Oihana::ID   => 5 ,
                    Oihana::NAME => 'Pro. (volume)' ,
                ] ,
            ],
            PricingCondition::class
        );

        $this->assertInstanceOf( PriceSegmentation::class , $condition->substitutesSegment ) ;
        $this->assertSame( 5 , $condition->substitutesSegment->id ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheFixedPriceEffect(): void
    {
        $condition = new Reflection()->hydrate
        (
            [
                Oihana::FIXED_PRICE =>
                [
                    Oihana::CURRENCY => 'EUR' ,
                    Oihana::VALUE    => 42.5 ,
                ] ,
            ],
            PricingCondition::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $condition->fixedPrice ) ;
        $this->assertSame( 'EUR' , $condition->fixedPrice->currency ) ;
        $this->assertSame( 42.5  , $condition->fixedPrice->value    ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testFreeFlag(): void
    {
        $condition = new Reflection()->hydrate
        (
            [ Oihana::FREE => true ],
            PricingCondition::class
        );

        $this->assertTrue( $condition->free ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheAdditionalProperty(): void
    {
        $condition = new Reflection()->hydrate
        (
            [
                Oihana::ADDITIONAL_PROPERTY =>
                [
                    [ Oihana::PROPERTY_ID => 'channel' , Oihana::VALUE => 'retail' ] ,
                    [ Oihana::PROPERTY_ID => 'priority' , Oihana::VALUE => 10 ] ,
                ] ,
            ],
            PricingCondition::class
        );

        $this->assertIsArray( $condition->additionalProperty ) ;
        $this->assertCount( 2 , $condition->additionalProperty ) ;
        $this->assertContainsOnlyInstancesOf( PropertyValue::class , $condition->additionalProperty ) ;

        $this->assertSame( 'channel' , $condition->additionalProperty[0]->propertyID ) ;
        $this->assertSame( 'retail'  , $condition->additionalProperty[0]->value      ) ;
        $this->assertSame( 'priority', $condition->additionalProperty[1]->propertyID ) ;
        $this->assertSame( 10        , $condition->additionalProperty[1]->value      ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheResolvedTargets(): void
    {
        $condition = new Reflection()->hydrate
        (
            [
                Oihana::CATEGORY   => [ Oihana::ID => '05'     , Oihana::NAME => 'Wines'      ] ,
                Oihana::CUSTOMER   => [ Oihana::ID => '600214' , Oihana::NAME => 'Acme'       ] ,
                Oihana::PRODUCT    => [ Oihana::ID => '70196'  , Oihana::NAME => 'Merlot'     ] ,
                Oihana::PROVIDER   => [ Oihana::ID => 'P-1'    , Oihana::NAME => 'Vineyard'   ] ,
                Oihana::SUBSIDIARY => [ Oihana::ID => 'S-1'    , Oihana::NAME => 'North'      ] ,
                Oihana::WAREHOUSE  => [ Oihana::ID => 'W-1'    , Oihana::NAME => 'Cellar'     ] ,
            ],
            PricingCondition::class
        );

        $this->assertInstanceOf( DefinedTerm::class , $condition->category   ) ;
        $this->assertInstanceOf( Customer::class    , $condition->customer   ) ;
        $this->assertInstanceOf( Product::class     , $condition->product    ) ;
        $this->assertInstanceOf( Provider::class    , $condition->provider   ) ;
        $this->assertInstanceOf( Subsidiary::class  , $condition->subsidiary ) ;
        $this->assertInstanceOf( Warehouse::class   , $condition->warehouse  ) ;

        $this->assertSame( '05'     , $condition->category->id   ) ;
        $this->assertSame( '600214' , $condition->customer->id   ) ;
        $this->assertSame( '70196'  , $condition->product->id    ) ;
        $this->assertSame( 'P-1'    , $condition->provider->id   ) ;
        $this->assertSame( 'S-1'    , $condition->subsidiary->id ) ;
        $this->assertSame( 'W-1'    , $condition->warehouse->id  ) ;
    }
}
