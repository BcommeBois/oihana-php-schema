<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\PermissionsTrait;

class PermissionsTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use PermissionsTrait; };

        $expected =
        [
            'PERMISSIONS'       => 'permissions'      ,
            'PERMISSIONS_COUNT' => 'permissionsCount' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
