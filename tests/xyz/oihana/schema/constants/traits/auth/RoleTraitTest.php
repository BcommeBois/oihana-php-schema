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
            'APPLICATION_TEMPLATES'       => 'applicationTemplates' ,
            'APPLICATION_TEMPLATES_COUNT' => 'applicationTemplatesCount' ,
            'COLOR'                       => 'color' ,
            'LEVEL'                       => 'level' ,
            'PERMISSIONS'                 => 'permissions' ,
            'PERMISSIONS_COUNT'           => 'permissionsCount' ,
            'PROTECTED'                   => 'protected' ,
            'SYSTEM'                      => 'system' ,
            'USERS'                       => 'users' ,
            'USERS_COUNT'                 => 'usersCount' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
