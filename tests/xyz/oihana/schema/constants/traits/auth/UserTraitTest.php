<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\UserTrait;

class UserTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use UserTrait; };

        $expected =
        [
            'ACTIVATED'                     => 'activated' ,
            'APP_META_DATA'                 => 'appMetadata' ,
            'APPLICATIONS'                  => 'applications' ,
            'BLOCKED_FOR'                   => 'blockedFor' ,
            'DEVICES'                       => 'devices' ,
            'FIRST_LOGIN_AT'                => 'firstLoginAt' ,
            'INVITATION_STATUS'             => 'invitationStatus' ,
            'LAST_LOGIN'                    => 'lastLogin' ,
            'LOGINS_COUNT'                  => 'loginsCount' ,
            'MAX_LEVEL'                     => 'maxLevel' ,
            'METADATA'                      => 'metadata' ,
            'PENDING_EMAIL'                 => 'pendingEmail' ,
            'PENDING_EMAIL_CODE_EXPIRES_AT' => 'pendingEmailCodeExpiresAt' ,
            'PENDING_EMAIL_CODE_HASH'       => 'pendingEmailCodeHash' ,
            'PENDING_EMAIL_SINCE'           => 'pendingEmailSince' ,
            'PERMISSIONS'                   => 'permissions' ,
            'PERMISSIONS_COUNT'             => 'permissionsCount' ,
            'ROLES'                         => 'roles' ,
            'ROLES_COUNT'                   => 'rolesCount' ,
            'SERVICES'                      => 'services' ,
            'SERVICES_COUNT'                => 'servicesCount' ,
            'SIGNED_UP'                     => 'signedUp' ,
            'STATUS'                        => 'status' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
