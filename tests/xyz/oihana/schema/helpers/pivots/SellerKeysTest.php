<?php

namespace tests\xyz\oihana\schema\helpers\pivots ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\people\Seller;

use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\business\BusinessIdentity;

use function xyz\oihana\schema\helpers\pivots\sellerKeys;

final class SellerKeysTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testReturnsEverySellerKeyDeduplicated(): void
    {
        $user = new User();
        $user->identities =
        [
            new BusinessIdentity([ BusinessIdentity::SUBJECT => new Seller([ '_key' => '147737218' , 'additionalType' => Seller::getSchemaType() ]) ]) ,
            new BusinessIdentity([ BusinessIdentity::SUBJECT => new Seller([ '_key' => '147737209' , 'additionalType' => Seller::getSchemaType() ]) ]) ,
            new BusinessIdentity([ BusinessIdentity::SUBJECT => new Seller([ '_key' => '147737218' , 'additionalType' => Seller::getSchemaType() ]) ]) ,
        ];

        $this->assertSame( [ '147737218' , '147737209' ] , sellerKeys( $user ) );
    }

    /**
     * @throws ReflectionException
     */
    public function testIgnoresIdentitiesWithoutASubjectKey(): void
    {
        $user = new User();
        $user->identities =
        [
            new BusinessIdentity([ BusinessIdentity::SUBJECT => new Seller([ 'additionalType' => Seller::getSchemaType() ]) ]) ,
        ];

        $this->assertSame( [] , sellerKeys( $user ) );
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsAnEmptyArrayWhenNoSellerIdentity(): void
    {
        $user = new User();
        $user->identities =
        [
            new BusinessIdentity([ BusinessIdentity::SUBJECT => new CustomerEmployee([ '_key' => '125752613' , 'additionalType' => CustomerEmployee::getSchemaType() ]) ]) ,
        ];

        $this->assertSame( [] , sellerKeys( $user ) );
        $this->assertSame( [] , sellerKeys( new User() ) );
    }
}
