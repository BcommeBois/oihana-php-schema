<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\InvitationTrait;

class InvitationTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use InvitationTrait; };

        $expected =
        [
            'REDIRECT_URL'            => 'redirectUrl' ,
            'SENT_AT'                 => 'sentAt' ,
            'SENT_COUNT'              => 'sentCount' ,
            'TOKEN'                   => 'token' ,
            'ACTION_STATUS_ACCEPTED'  => 'accepted' ,
            'ACTION_STATUS_CANCELLED' => 'cancelled' ,
            'ACTION_STATUS_EXPIRED'   => 'expired' ,
            'ACTION_STATUS_PENDING'   => 'pending' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
