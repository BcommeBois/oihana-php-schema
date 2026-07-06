<?php

namespace tests\xyz\oihana\schema\places ;

use PHPUnit\Framework\TestCase;

use org\schema\PostalAddress;

use xyz\oihana\schema\places\Office;

class OfficeTest extends TestCase
{
    public function testMagicSetHandlesPostalAddressProperties(): void
    {
        $office = new Office() ;

        $office->streetAddress = '12 rue des Bois' ;

        $this->assertInstanceOf( PostalAddress::class , $office->address ) ;
        $this->assertSame( '12 rue des Bois' , $office->address->streetAddress ) ;
    }
}
