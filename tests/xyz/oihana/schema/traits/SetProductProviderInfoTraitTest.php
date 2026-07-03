<?php

namespace tests\xyz\oihana\schema\traits ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\products\ProductProviderInfo;
use xyz\oihana\schema\traits\SetProductProviderInfoTrait;

/**
 * Host exposing the {@see SetProductProviderInfoTrait} with the `productInfo`
 * and `id` properties expected by the trait.
 */
class SetProductProviderInfoHost
{
    use SetProductProviderInfoTrait ;

    public mixed $id = null ;

    public ?ProductProviderInfo $productInfo = null ;

    public function set( string $name , mixed $value ): bool
    {
        return $this->setProductProviderInfoProperty( $name , $value ) ;
    }
}

class SetProductProviderInfoTraitTest extends TestCase
{
    // ---- setProductProviderInfoProperty

    public function testSetCreatesTheProductInfo(): void
    {
        $host = new SetProductProviderInfoHost() ;

        $this->assertTrue( $host->set( Oihana::BUYING_PRICE , 12.5 ) ) ;

        $this->assertInstanceOf( ProductProviderInfo::class , $host->productInfo ) ;
        $this->assertSame( 12.5 , $host->productInfo->buyingPrice ) ;
    }

    public function testSetCopiesTheHostIdOnTheProductInfo(): void
    {
        $host = new SetProductProviderInfoHost() ;
        $host->id = 'providers/105997' ;

        $host->set( Oihana::BUYING_PRICE , 12.5 ) ;

        $this->assertSame( 'providers/105997' , $host->productInfo->id ) ;
    }

    public function testSetClearsAPropertyWithANullValue(): void
    {
        $host = new SetProductProviderInfoHost() ;
        $host->set( Oihana::BUYING_PRICE , 12.5 ) ;

        $this->assertTrue( $host->set( Oihana::BUYING_PRICE , null ) ) ;
        $this->assertNull( $host->productInfo->buyingPrice ) ;
    }

    public function testSetIgnoresANullValueWithoutProductInfo(): void
    {
        $host = new SetProductProviderInfoHost() ;

        $this->assertFalse( $host->set( Oihana::BUYING_PRICE , null ) ) ;
        $this->assertNull( $host->productInfo ) ;
    }

    // ---- setProductProviderInfoProperties

    public function testSetPropertiesHandlesTheSupportedProperties(): void
    {
        $host = new SetProductProviderInfoHost() ;

        $this->assertTrue( $host->setProductProviderInfoProperties( Oihana::BUYING_PRICE                              , 12.5   ) ) ;
        $this->assertTrue( $host->setProductProviderInfoProperties( Oihana::BUYING_PRICE_MARGIN                       , 1.2    ) ) ;
        $this->assertTrue( $host->setProductProviderInfoProperties( Oihana::BUYING_PRICE_REFERENCE_QUANTITY           , 100    ) ) ;
        $this->assertTrue( $host->setProductProviderInfoProperties( Oihana::BUYING_PRICE_REFERENCE_QUANTITY_UNIT_CODE , 'MTK'  ) ) ;
        $this->assertTrue( $host->setProductProviderInfoProperties( Oihana::HAS_QUANTITY_DISCOUNT                     , true   ) ) ;
        $this->assertTrue( $host->setProductProviderInfoProperties( Oihana::NEXT_BUYING_PRICE                         , 13.1   ) ) ;
        $this->assertTrue( $host->setProductProviderInfoProperties( Oihana::NEXT_BUYING_PRICE_DATE                    , '2026-08-01' ) ) ;
        $this->assertTrue( $host->setProductProviderInfoProperties( Oihana::PRIMARY                                   , true   ) ) ;

        $this->assertSame( 12.5 , $host->productInfo->buyingPrice ) ;
        $this->assertSame( 13.1 , $host->productInfo->nextBuyingPrice ) ;
    }

    public function testSetPropertiesRejectsUnknownProperty(): void
    {
        $host = new SetProductProviderInfoHost() ;

        $this->assertFalse( $host->setProductProviderInfoProperties( 'unknownProperty' , 12.5 ) ) ;
        $this->assertNull( $host->productInfo ) ;
    }
}
