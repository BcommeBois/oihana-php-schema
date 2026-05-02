<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\UsersTrait;

class UsersTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use UsersTrait; };

        $expected =
        [
            'USERS'       => 'users'      ,
            'USERS_COUNT' => 'usersCount' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
