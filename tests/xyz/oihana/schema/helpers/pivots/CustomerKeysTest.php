<?php

namespace tests\xyz\oihana\schema\helpers\pivots ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Organization;

use xyz\oihana\schema\people\CustomerEmployee;
use xyz\oihana\schema\people\Seller;

use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\business\BusinessIdentity;

use function xyz\oihana\schema\helpers\pivots\customerKeys;

final class CustomerKeysTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testReturnsEveryCustomerKeyDeduplicated(): void
    {
        $user = new User();
        $user->identities =
        [
            $this->contactOf( '125752613' , '137285125' ) ,
            $this->contactOf( '125752620' , '137285130' ) ,
            $this->contactOf( '125752640' , '137285125' ) ,
        ];

        $this->assertSame( [ '137285125' , '137285130' ] , customerKeys( $user ) );
    }

    /**
     * @throws ReflectionException
     */
    public function testIgnoresIdentitiesWithoutAWorksForKey(): void
    {
        $user = new User();
        $user->identities =
        [
            new BusinessIdentity([ BusinessIdentity::SUBJECT => new CustomerEmployee([ '_key' => '125752613' , 'additionalType' => CustomerEmployee::getSchemaType() ]) ]) ,
        ];

        $this->assertSame( [] , customerKeys( $user ) );
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsAnEmptyArrayWhenNoCustomerIdentity(): void
    {
        $user = new User();
        $user->identities =
        [
            new BusinessIdentity([ BusinessIdentity::SUBJECT => new Seller([ '_key' => '147737218' , 'additionalType' => Seller::getSchemaType() ]) ]) ,
        ];

        $this->assertSame( [] , customerKeys( $user ) );
        $this->assertSame( [] , customerKeys( new User() ) );
    }

    /**
     * @throws ReflectionException
     */
    private function contactOf( string $employeeKey , string $organizationKey ) : BusinessIdentity
    {
        $employee = new CustomerEmployee([ '_key' => $employeeKey , 'additionalType' => CustomerEmployee::getSchemaType() ]);
        $employee->worksFor = new Organization([ '_key' => $organizationKey ]);

        return new BusinessIdentity([ BusinessIdentity::SUBJECT => $employee ]);
    }
}
