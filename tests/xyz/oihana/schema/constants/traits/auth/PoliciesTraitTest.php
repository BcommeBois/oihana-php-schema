<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\PoliciesTrait;

class PoliciesTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use PoliciesTrait; };

        $expected =
        [
            'POLICIES'       => 'policies'      ,
            'POLICIES_COUNT' => 'policiesCount' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
