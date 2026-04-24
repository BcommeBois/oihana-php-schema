<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\ScopeTrait;

class ScopeTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use ScopeTrait; };

        $expected =
        [
            'SCOPES'       => 'scopes' ,
            'SCOPES_COUNT' => 'scopesCount' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
