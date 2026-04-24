<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\SessionTrait;

class SessionTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use SessionTrait; };

        $expected =
        [
            'CLIENT_ID'  => 'clientId' ,
            'CURRENT'    => 'current' ,
            'EXPIRES_AT' => 'expiresAt' ,
            'IP'         => 'ip' ,
            'REVOKED_AT' => 'revokedAt' ,
            'TOKEN_HASH' => 'tokenHash' ,
            'USER_AGENT' => 'userAgent' ,
            'USER_ID'    => 'userId' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
