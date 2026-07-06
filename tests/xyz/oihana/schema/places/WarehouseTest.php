<?php

namespace tests\xyz\oihana\schema\places ;

use PHPUnit\Framework\TestCase;

use org\schema\PostalAddress;

use xyz\oihana\schema\places\Warehouse;

class WarehouseTest extends TestCase
{
    public function testMagicSetHandlesPostalAddressProperties(): void
    {
        $warehouse = new Warehouse() ;

        $warehouse->streetAddress = '12 rue des Bois' ;

        $this->assertInstanceOf( PostalAddress::class , $warehouse->address ) ;
        $this->assertSame( '12 rue des Bois' , $warehouse->address->streetAddress ) ;
    }
}
