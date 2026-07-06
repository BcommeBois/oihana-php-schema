<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\enumerations\PriceComponentTypeEnumeration;
use xyz\oihana\schema\enumerations\PriceComponentType;

class PriceComponentTypeTest extends TestCase
{
    public function testIsPriceComponentTypeEnumeration(): void
    {
        $this->assertInstanceOf( PriceComponentTypeEnumeration::class , new PriceComponentType() );
    }

    public function testInheritsTheSchemaOrgConstants(): void
    {
        $this->assertSame( PriceComponentTypeEnumeration::DOWN_PAYMENT , PriceComponentType::DOWN_PAYMENT );
    }

    public function testNewAdjustmentRelatedConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/Deposit'         , PriceComponentType::DEPOSIT          );
        $this->assertSame( 'https://schema.oihana.xyz/Discount'        , PriceComponentType::DISCOUNT         );
        $this->assertSame( 'https://schema.oihana.xyz/EnvironmentalFee', PriceComponentType::ENVIRONMENTAL_FEE );
        $this->assertSame( 'https://schema.oihana.xyz/Packaging'       , PriceComponentType::PACKAGING        );
        $this->assertSame( 'https://schema.oihana.xyz/Surcharge'       , PriceComponentType::SURCHARGE        );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( PriceComponentType::includes( PriceComponentType::DISCOUNT ) );
        $this->assertFalse( PriceComponentType::includes( 'unknown' ) );
    }
}
