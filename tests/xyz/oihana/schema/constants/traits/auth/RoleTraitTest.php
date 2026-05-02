<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\RoleTrait;

class RoleTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use RoleTrait; };

        $expected =
        [
            'DEFAULT'           => 'default' ,
            'LEVEL'             => 'level' ,

            // ---- traits

            'COLOR'             => 'color' ,
            'PROTECTED'         => 'protected' ,
            'SYSTEM'            => 'system' ,
            'PERMISSIONS'       => 'permissions' ,
            'PERMISSIONS_COUNT' => 'permissionsCount' ,
            'POLICIES'          => 'policies' ,
            'POLICIES_COUNT'    => 'policiesCount' ,
            'USERS'             => 'users' ,
            'USERS_COUNT'       => 'usersCount' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
