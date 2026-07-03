<?php

namespace tests\xyz\oihana\schema\organizations ;

use PHPUnit\Framework\TestCase;

use org\schema\PostalAddress;
use org\schema\PropertyValue;

use xyz\oihana\schema\constants\CustomerAdditionalProperty;
use xyz\oihana\schema\organizations\Company;
use xyz\oihana\schema\organizations\Customer;

class CustomerTest extends TestCase
{
    public function testIsCompany(): void
    {
        $this->assertInstanceOf( Company::class , new Customer() );
    }

    public function testMagicSetHandlesAdditionalProperties(): void
    {
        $customer = new Customer() ;

        $customer->showApplications     = '1' ;
        $customer->invoiceIssueInterval = '3' ;

        $this->assertCount( 2 , $customer->additionalProperty ) ;
        $this->assertContainsOnlyInstancesOf( PropertyValue::class , $customer->additionalProperty ) ;

        $this->assertSame( CustomerAdditionalProperty::SHOW_APPLICATIONS , $customer->additionalProperty[0]->propertyID ) ;
        $this->assertTrue( $customer->additionalProperty[0]->value ) ;

        $this->assertSame( 3 , $customer->additionalProperty[1]->value ) ;
    }

    public function testMagicSetFallsBackToCompanyProperties(): void
    {
        $customer = new Customer() ;

        $customer->streetAddress = '12 rue des Bois' ;

        $this->assertInstanceOf( PostalAddress::class , $customer->address ) ;
        $this->assertSame( '12 rue des Bois' , $customer->address->streetAddress ) ;
        $this->assertNull( $customer->additionalProperty ) ;
    }

    public function testSetAdditionalPropertiesRejectsNonStringOrUnknownValues(): void
    {
        $customer = new Customer() ;

        $this->assertFalse( $customer->setAdditionalProperties( CustomerAdditionalProperty::SHOW_APPLICATIONS , 1 ) ) ;
        $this->assertFalse( $customer->setAdditionalProperties( CustomerAdditionalProperty::SHOW_APPLICATIONS , null ) ) ;
        $this->assertFalse( $customer->setAdditionalProperties( 'unknownProperty' , '1' ) ) ;
        $this->assertNull( $customer->additionalProperty ) ;
    }
}
