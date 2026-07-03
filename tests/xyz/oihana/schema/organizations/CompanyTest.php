<?php

namespace tests\xyz\oihana\schema\organizations ;

use PHPUnit\Framework\TestCase;

use org\schema\ContactPoint;
use org\schema\organizations\Corporation;
use org\schema\PostalAddress;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\organizations\Company;

class CompanyTest extends TestCase
{
    public function testIsCorporation(): void
    {
        $this->assertInstanceOf( Corporation::class , new Company() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , Company::CONTEXT );
    }

    public function testMagicSetHandlesPostalAddressProperties(): void
    {
        $company = new Company() ;

        $company->streetAddress = '12 rue des Bois' ;
        $company->addressEmail  = 'contact@example.com' ;

        $this->assertInstanceOf( PostalAddress::class , $company->address ) ;
        $this->assertSame( '12 rue des Bois'     , $company->address->streetAddress ) ;
        $this->assertSame( 'contact@example.com' , $company->address->email         ) ;
    }

    public function testMagicSetHandlesContactPointProperties(): void
    {
        $company = new Company() ;

        $company->default_telephone = '0102030405' ;

        $this->assertIsArray( $company->contactPoint ) ;
        $this->assertInstanceOf( ContactPoint::class , $company->contactPoint[0] ) ;
        $this->assertSame( '0102030405' , $company->contactPoint[0]->telephone ) ;
    }

    public function testSetCompanyPropertiesRejectsUnknownProperty(): void
    {
        $company = new Company() ;

        $this->assertFalse( $company->setCompanyProperties( 'unknownProperty' , 'value' ) ) ;
    }
}
