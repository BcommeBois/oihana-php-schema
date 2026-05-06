<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\KeyfileTrait;

class KeyfileTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use KeyfileTrait; };

        $expected =
        [
            'API_BASE_URL' => 'apiBaseUrl' ,
            'AUDIENCE'     => 'audience'   ,
            'CLIENT_ID'    => 'clientId'   ,
            'ISSUER'       => 'issuer'     ,
            'KEY'          => 'key'        ,
            'KEY_ID'       => 'keyId'      ,
            'SCOPE'        => 'scope'      ,
            'TYPE'         => 'type'       ,
            'USER_ID'      => 'userId'     ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
