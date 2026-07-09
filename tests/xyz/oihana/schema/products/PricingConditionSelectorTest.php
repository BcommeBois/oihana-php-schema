<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;

use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\PricingItemScope;
use xyz\oihana\schema\enumerations\PricingTargetScope;
use xyz\oihana\schema\products\PricingConditionSelector;

class PricingConditionSelectorTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new PricingConditionSelector() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , PricingConditionSelector::CONTEXT );
    }

    public function testConstantsAreWiredOnTheAggregator(): void
    {
        $this->assertSame( 'areaServed'    , Oihana::AREA_SERVED    );
        $this->assertSame( 'categoryLevel' , Oihana::CATEGORY_LEVEL );
        $this->assertSame( 'customerId'    , Oihana::CUSTOMER_ID    );
        $this->assertSame( 'customerScope' , Oihana::CUSTOMER_SCOPE );
        $this->assertSame( 'itemId'        , Oihana::ITEM_ID        );
        $this->assertSame( 'itemScope'     , Oihana::ITEM_SCOPE     );
        $this->assertSame( 'providerId'    , Oihana::PROVIDER_ID    );
    }

    public function testDefaults(): void
    {
        $selector = new PricingConditionSelector() ;

        $this->assertNull( $selector->areaServed    ?? null );
        $this->assertNull( $selector->categoryLevel ?? null );
        $this->assertNull( $selector->customerId    ?? null );
        $this->assertNull( $selector->customerScope ?? null );
        $this->assertNull( $selector->itemId        ?? null );
        $this->assertNull( $selector->itemScope     ?? null );
        $this->assertNull( $selector->providerId    ?? null );
    }

    public function testConstructorHydratesProperties(): void
    {
        $selector = new PricingConditionSelector
        ([
            Oihana::CUSTOMER_SCOPE => PricingTargetScope::GROUP ,
            Oihana::CUSTOMER_ID    => '600214' ,
            Oihana::ITEM_SCOPE     => PricingItemScope::CATEGORY ,
            Oihana::ITEM_ID        => '05' ,
            Oihana::CATEGORY_LEVEL => 1 ,
            Oihana::AREA_SERVED    => '600' ,
            Oihana::PROVIDER_ID    => 'P-42' ,
        ]);

        $this->assertSame( PricingTargetScope::GROUP    , $selector->customerScope ) ;
        $this->assertSame( '600214'                     , $selector->customerId    ) ;
        $this->assertSame( PricingItemScope::CATEGORY   , $selector->itemScope     ) ;
        $this->assertSame( '05'                         , $selector->itemId        ) ;
        $this->assertSame( 1                            , $selector->categoryLevel ) ;
        $this->assertSame( '600'                        , $selector->areaServed    ) ;
        $this->assertSame( 'P-42'                       , $selector->providerId    ) ;
    }
}
