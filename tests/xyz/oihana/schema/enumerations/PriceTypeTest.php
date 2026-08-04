<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\enumerations\PriceTypeEnumeration;
use xyz\oihana\schema\enumerations\PriceType;

class PriceTypeTest extends TestCase
{
    public function testIsPriceTypeEnumeration(): void
    {
        $this->assertInstanceOf( PriceTypeEnumeration::class , new PriceType() );
    }

    public function testInheritsTheSchemaOrgConstants(): void
    {
        $this->assertSame( PriceTypeEnumeration::MINIMUM_ADVERTISED_PRICE , PriceType::MINIMUM_ADVERTISED_PRICE );
        $this->assertSame( 'https://schema.org/MinimumAdvertisedPrice'    , PriceType::MINIMUM_ADVERTISED_PRICE );
        $this->assertSame( 'https://schema.org/StrikethroughPrice'        , PriceType::STRIKE_THROUGH_PRICE     );
    }

    public function testHousePricingConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/BuyingPriceDiscounted'  , PriceType::BUYING_DISCOUNTED  );
        $this->assertSame( 'https://schema.oihana.xyz/BuyingPriceReference'   , PriceType::BUYING_REFERENCE   );
        $this->assertSame( 'https://schema.oihana.xyz/BuyingPriceWithMargin'  , PriceType::BUYING_WITH_MARGIN );
        $this->assertSame( 'https://schema.oihana.xyz/COGS'                   , PriceType::COGS               );
        $this->assertSame( 'https://schema.oihana.xyz/LoadedRate'             , PriceType::LOADED_RATE        );
        $this->assertSame( 'https://schema.oihana.xyz/SellingPriceForced'     , PriceType::SELLING_FORCED     );
        $this->assertSame( 'https://schema.oihana.xyz/SellingPriceReference'  , PriceType::SELLING_REFERENCE  );
    }

    public function testSellingUnitPriceConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/SellingUnitPrice' , PriceType::SELLING_UNIT_PRICE );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( PriceType::includes( PriceType::SELLING_UNIT_PRICE ) );
        $this->assertTrue ( PriceType::includes( PriceType::MINIMUM_ADVERTISED_PRICE ) );
        $this->assertFalse( PriceType::includes( 'unknown' ) );
    }
}
