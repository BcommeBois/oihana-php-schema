<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;

use xyz\oihana\schema\enumerations\BusinessIdentityRole;

class BusinessIdentityRoleTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertTrue( is_subclass_of( BusinessIdentityRole::class , Enumeration::class ) );
    }

    public function testValues(): void
    {
        $this->assertSame( 'customer'        , BusinessIdentityRole::CUSTOMER );
        $this->assertSame( 'customerContact' , BusinessIdentityRole::CUSTOMER_CONTACT );
        $this->assertSame( 'deliverer'       , BusinessIdentityRole::DELIVERER );
        $this->assertSame( 'provider'        , BusinessIdentityRole::PROVIDER );
        $this->assertSame( 'seller'          , BusinessIdentityRole::SELLER );
    }

    public function testEnumsAreUnique(): void
    {
        $enums = BusinessIdentityRole::enums();

        $this->assertContains( 'customer'        , $enums );
        $this->assertContains( 'customerContact' , $enums );
        $this->assertContains( 'deliverer'       , $enums );
        $this->assertContains( 'provider'        , $enums );
        $this->assertContains( 'seller'          , $enums );
        $this->assertSame( count( $enums ) , count( array_unique( $enums ) ) );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( BusinessIdentityRole::includes( 'seller' ) );
        $this->assertFalse( BusinessIdentityRole::includes( 'unknown-role' ) );
    }
}
