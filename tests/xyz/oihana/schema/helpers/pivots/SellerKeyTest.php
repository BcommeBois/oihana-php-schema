<?php

namespace tests\xyz\oihana\schema\helpers\pivots ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\people\Seller;

use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\business\BusinessIdentity;

use function xyz\oihana\schema\helpers\pivots\sellerKey;

final class SellerKeyTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testReturnsTheSellerKey(): void
    {
        $user = new User();
        $user->identities =
        [
            new BusinessIdentity([ BusinessIdentity::SUBJECT => new Seller([ '_key' => '147737218' , 'additionalType' => Seller::getSchemaType() ]) ]) ,
        ];

        $this->assertSame( '147737218' , sellerKey( $user ) );
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsNullWhenNoSellerIdentity(): void
    {
        $user = new User();
        $user->identities =
        [
            new BusinessIdentity([ BusinessIdentity::SUBJECT => new CustomerEmployee([ '_key' => '125752613' , 'additionalType' => CustomerEmployee::getSchemaType() ]) ]) ,
        ];

        $this->assertNull( sellerKey( $user ) );
    }

    public function testReturnsNullWhenNoIdentities(): void
    {
        $this->assertNull( sellerKey( new User() ) );
    }
}
