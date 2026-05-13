<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\PendingRevocationTrait;

class PendingRevocationTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use PendingRevocationTrait; };

        $expected =
        [
            'ATTEMPTS'        => 'attempts' ,
            'LAST_ATTEMPT_AT' => 'lastAttemptAt' ,
            'LAST_ERROR'      => 'lastError' ,
            'PROVIDER'        => 'provider' ,
            'REASON'          => 'reason' ,
            'TARGET_ID'       => 'targetId' ,
            'TARGET_TYPE'     => 'targetType' ,
            'USER_IDENTIFIER' => 'userIdentifier' ,
            'USER_KEY'        => 'userKey' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
