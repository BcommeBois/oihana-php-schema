<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\ClientIdTrait;

class ClientIdTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use ClientIdTrait; };

        $this->assertSame( 'clientId' , $host::CLIENT_ID );
    }
}
