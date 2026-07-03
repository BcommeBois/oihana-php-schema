<?php

namespace tests\xyz\oihana\schema\organizations ;

use PHPUnit\Framework\TestCase;

use org\schema\PostalAddress;

use xyz\oihana\schema\organizations\Company;
use xyz\oihana\schema\organizations\Provider;
use xyz\oihana\schema\products\ProductProviderInfo;

class ProviderTest extends TestCase
{
    public function testIsCompany(): void
    {
        $this->assertInstanceOf( Company::class , new Provider() );
    }

    public function testMagicSetHandlesCompanyProperties(): void
    {
        $provider = new Provider() ;

        $provider->streetAddress = '12 rue des Bois' ;

        $this->assertInstanceOf( PostalAddress::class , $provider->address ) ;
        $this->assertSame( '12 rue des Bois' , $provider->address->streetAddress ) ;
    }

    public function testMagicSetHandlesProductProviderInfoProperties(): void
    {
        $provider = new Provider() ;

        $provider->buyingPrice = 12.5 ;

        $this->assertInstanceOf( ProductProviderInfo::class , $provider->productInfo ) ;
        $this->assertSame( 12.5 , $provider->productInfo->buyingPrice ) ;
    }
}
