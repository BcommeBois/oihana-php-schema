<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\UserTrait;

class UserTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use UserTrait; };

        $expected =
        [
            'ACTIVATED'         => 'activated' ,
            'APP_META_DATA'     => 'appMetadata' ,
            'APPLICATIONS'      => 'applications' ,
            'BLOCKED_FOR'       => 'blockedFor' ,
            'DEVICES'           => 'devices' ,
            'FIRST_LOGIN_AT'    => 'firstLoginAt' ,
            'LAST_LOGIN'        => 'lastLogin' ,
            'LOGINS_COUNT'      => 'loginsCount' ,
            'METADATA'          => 'metadata' ,
            'PERMISSIONS'       => 'permissions' ,
            'PERMISSIONS_COUNT' => 'permissionsCount' ,
            'ROLES'             => 'roles' ,
            'ROLES_COUNT'       => 'rolesCount' ,
            'SIGNED_UP'         => 'signedUp' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
