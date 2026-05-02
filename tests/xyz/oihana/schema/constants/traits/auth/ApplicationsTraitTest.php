<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\ApplicationsTrait;

class ApplicationsTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use ApplicationsTrait; };

        $expected =
        [
            'APPLICATIONS'       => 'applications'      ,
            'APPLICATIONS_COUNT' => 'applicationsCount' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
