<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\OAuthClientTrait;

class OAuthClientTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use OAuthClientTrait; };

        $expected =
        [
            'APP_ID'    => 'appId' ,
            'CLIENT_ID' => 'clientId' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
