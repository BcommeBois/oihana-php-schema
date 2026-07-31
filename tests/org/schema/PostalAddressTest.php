<?php

namespace tests\org\schema ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\constants\Schema;
use org\schema\ContactPoint;
use org\schema\PostalAddress;

class PostalAddressTest extends TestCase
{
    public function testIsContactPoint(): void
    {
        $this->assertInstanceOf( ContactPoint::class , new PostalAddress() );
    }

    public function testAddressDepartmentDefaultsToNull(): void
    {
        $address = new PostalAddress();

        $this->assertNull( $address->addressDepartment ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorCopiesAddressDepartment(): void
    {
        $address = new PostalAddress( [ Schema::ADDRESS_DEPARTMENT => 'Pyrénées-Atlantiques' ] );

        $this->assertSame( 'Pyrénées-Atlantiques' , $address->addressDepartment );
    }

    public function testAddressDepartmentAssignment(): void
    {
        $address = new PostalAddress();

        $address->addressDepartment = 'Gironde' ;

        $this->assertSame( 'Gironde' , $address->addressDepartment );
    }

    public function testAddressDepartmentPropertyNameConstants(): void
    {
        $this->assertSame( 'addressDepartment'         , Schema::ADDRESS_DEPARTMENT );
        $this->assertSame( 'address.addressDepartment' , Schema::FULL_ADDRESS_DEPARTMENT );
    }
}
