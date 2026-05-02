<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\RolesTrait;

class RolesTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use RolesTrait; };

        $expected =
        [
            'ROLES'       => 'roles'      ,
            'ROLES_COUNT' => 'rolesCount' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
