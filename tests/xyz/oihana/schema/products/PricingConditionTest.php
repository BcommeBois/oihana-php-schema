<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\PriceComponentType;
use xyz\oihana\schema\enumerations\PricingItemScope;
use xyz\oihana\schema\enumerations\PricingTargetScope;
use xyz\oihana\schema\products\PriceSegmentation;
use xyz\oihana\schema\products\PricingCondition;
use xyz\oihana\schema\products\PricingConditionSelector;

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
        $this->assertSame( 'adjustment'          , Oihana::ADJUSTMENT          );
        $this->assertSame( 'excludedCustomers'   , Oihana::EXCLUDED_CUSTOMERS  );
        $this->assertSame( 'excludedProducts'    , Oihana::EXCLUDED_PRODUCTS   );
        $this->assertSame( 'quantityDiscount'    , Oihana::QUANTITY_DISCOUNT   );
        $this->assertSame( 'selector'            , Oihana::SELECTOR            );
        $this->assertSame( 'substitutesSegment'  , Oihana::SUBSTITUTES_SEGMENT );
        $this->assertSame( 'validFrom'           , Oihana::VALID_FROM          );
        $this->assertSame( 'validThrough'        , Oihana::VALID_THROUGH       );
    }

    public function testDefaults(): void
    {
        $condition = new PricingCondition() ;

        $this->assertNull( $condition->adjustment         ?? null );
        $this->assertNull( $condition->excludedCustomers  ?? null );
        $this->assertNull( $condition->excludedProducts   ?? null );
        $this->assertNull( $condition->quantityDiscount   ?? null );
        $this->assertNull( $condition->selector           ?? null );
        $this->assertNull( $condition->substitutesSegment ?? null );
        $this->assertNull( $condition->validFrom          ?? null );
        $this->assertNull( $condition->validThrough       ?? null );
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
                    Oihana::TYPE       => PriceComponentType::DISCOUNT ,
                    Oihana::PERCENTAGE => 10 ,
                ] ,
            ],
            PricingCondition::class
        );

        $this->assertInstanceOf( Adjustment::class , $condition->adjustment ) ;
        $this->assertSame( PriceComponentType::DISCOUNT , $condition->adjustment->type ) ;
        $this->assertSame( 10 , $condition->adjustment->percentage ) ;
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
}
