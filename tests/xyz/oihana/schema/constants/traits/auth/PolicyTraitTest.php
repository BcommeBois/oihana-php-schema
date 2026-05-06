<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\PolicyTrait;

class PolicyTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use PolicyTrait; };

        $expected =
        [
            'APPLICATIONS'       => 'applications' ,
            'APPLICATIONS_COUNT' => 'applicationsCount' ,
            'COLOR'              => 'color' ,
            'PERMISSIONS'        => 'permissions' ,
            'PERMISSIONS_COUNT'  => 'permissionsCount' ,
            'PROTECTED'          => 'protected' ,
            'ROLES'              => 'roles' ,
            'ROLES_COUNT'        => 'rolesCount' ,
            'SERVICES'           => 'services' ,
            'SERVICES_COUNT'     => 'servicesCount' ,
            'SYSTEM'             => 'system' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
