<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\ServicesTrait;

class ServicesTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use ServicesTrait; };

        $expected =
        [
            'SERVICES'       => 'services'      ,
            'SERVICES_COUNT' => 'servicesCount' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
