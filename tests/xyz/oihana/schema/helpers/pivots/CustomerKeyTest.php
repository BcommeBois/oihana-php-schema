<?php

namespace tests\xyz\oihana\schema\helpers\pivots ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Organization;

use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\people\Seller;

use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\business\BusinessIdentity;

use function xyz\oihana\schema\helpers\pivots\customerKey;

final class CustomerKeyTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testReturnsTheCustomerOrganizationKey(): void
    {
        $employee = new CustomerEmployee([ '_key' => '125752613' , 'additionalType' => CustomerEmployee::getSchemaType() ]);
        $employee->worksFor = new Organization([ '_key' => '137285125' ]);

        $user = new User();
        $user->identities = [ new BusinessIdentity([ BusinessIdentity::SUBJECT => $employee ]) ];

        $this->assertSame( '137285125' , customerKey( $user ) );
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsNullWhenNoCustomerIdentity(): void
    {
        $user = new User();
        $user->identities =
        [
            new BusinessIdentity([ BusinessIdentity::SUBJECT => new Seller([ '_key' => '147737218' , 'additionalType' => Seller::getSchemaType() ]) ]) ,
        ];

        $this->assertNull( customerKey( $user ) );
    }

    public function testReturnsNullWhenNoIdentities(): void
    {
        $this->assertNull( customerKey( new User() ) );
    }
}
