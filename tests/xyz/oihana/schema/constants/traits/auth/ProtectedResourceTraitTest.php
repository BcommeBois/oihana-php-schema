<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\ProtectedResourceTrait;

class ProtectedResourceTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use ProtectedResourceTrait; };

        $expected =
        [
            'COLOR'     => 'color'     ,
            'PROTECTED' => 'protected' ,
            'SYSTEM'    => 'system'    ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
