<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\ServiceTrait;

class ServiceTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use ServiceTrait; };

        $expected =
        [
            'ALLOWED_IPS'      => 'allowedIPs'      ,
            'CLIENT_ID'        => 'clientId'        ,
            'CREATED_BY'       => 'createdBy'       ,
            'DISABLED_AT'      => 'disabledAt'      ,
            'DISABLED_BY'      => 'disabledBy'      ,
            'DISABLED_REASON'  => 'disabledReason'  ,
            'EXPIRES_AT'       => 'expiresAt'       ,
            'KEY_ID'           => 'keyId'           ,
            'KEYFILE'          => 'keyfile'         ,
            'LAST_SEEN_IP'     => 'lastSeenIP'      ,
            'LAST_USED_AT'     => 'lastUsedAt'      ,
            'METADATA'         => 'metadata'        ,
            'PERMISSIONS'      => 'permissions'     ,
            'PERMISSIONS_COUNT'=> 'permissionsCount',
            'POLICIES'         => 'policies'        ,
            'POLICIES_COUNT'   => 'policiesCount'   ,
            'PROTECTED'        => 'protected'       ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
