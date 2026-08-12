<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use xyz\oihana\schema\enumerations\FeeUnresolvedReason;

class FeeUnresolvedReasonTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new FeeUnresolvedReason() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/FeeUnresolvedReason#MissingFeeRate'        , FeeUnresolvedReason::MISSING_FEE_RATE        );
        $this->assertSame( 'https://schema.oihana.xyz/FeeUnresolvedReason#MissingProductMeasure' , FeeUnresolvedReason::MISSING_PRODUCT_MEASURE );
        $this->assertSame( 'https://schema.oihana.xyz/FeeUnresolvedReason#UnknownPackageContent' , FeeUnresolvedReason::UNKNOWN_PACKAGE_CONTENT );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( FeeUnresolvedReason::includes( FeeUnresolvedReason::MISSING_FEE_RATE        ) );
        $this->assertTrue ( FeeUnresolvedReason::includes( FeeUnresolvedReason::MISSING_PRODUCT_MEASURE ) );
        $this->assertTrue ( FeeUnresolvedReason::includes( FeeUnresolvedReason::UNKNOWN_PACKAGE_CONTENT ) );
        $this->assertFalse( FeeUnresolvedReason::includes( 'unknown' ) );
    }
}
