<?php

namespace tests\xyz\oihana\schema\constants\traits ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\AuthTrait;

class AuthTraitTest extends TestCase
{
    public function testComposesAllAuthTraits(): void
    {
        $host = new class { use AuthTrait; };
        $class = $host::class;

        // ApplicationTrait
        $this->assertSame( 'allowedIPs' , constant( "$class::ALLOWED_IPS"  ) );
        $this->assertSame( 'default'    , constant( "$class::DEFAULT"      ) );
        $this->assertSame( 'expiresAt'  , constant( "$class::EXPIRES_AT"   ) );
        $this->assertSame( 'lastUsedAt' , constant( "$class::LAST_USED_AT" ) );
        $this->assertSame( 'metadata'   , constant( "$class::METADATA"     ) );

        // ApplicationTemplateTrait
        $this->assertSame( 'applicationTemplates'      , constant( "$class::APPLICATION_TEMPLATES"       ) );
        $this->assertSame( 'applicationTemplatesCount' , constant( "$class::APPLICATION_TEMPLATES_COUNT" ) );
        $this->assertSame( 'selfService'               , constant( "$class::SELF_SERVICE"                ) );

        // InvitationTrait
        $this->assertSame( 'redirectUrl' , constant( "$class::REDIRECT_URL"            ) );
        $this->assertSame( 'sentAt'      , constant( "$class::SENT_AT"                 ) );
        $this->assertSame( 'pending'     , constant( "$class::ACTION_STATUS_PENDING"   ) );

        // RoleTrait
        $this->assertSame( 'level'            , constant( "$class::LEVEL"             ) );
        $this->assertSame( 'permissions'      , constant( "$class::PERMISSIONS"       ) );
        $this->assertSame( 'permissionsCount' , constant( "$class::PERMISSIONS_COUNT" ) );

        // ScopeTrait
        $this->assertSame( 'scopes'      , constant( "$class::SCOPES"       ) );
        $this->assertSame( 'scopesCount' , constant( "$class::SCOPES_COUNT" ) );

        // SessionTrait
        $this->assertSame( 'clientId'  , constant( "$class::CLIENT_ID"  ) );
        $this->assertSame( 'tokenHash' , constant( "$class::TOKEN_HASH" ) );
        $this->assertSame( 'userId'    , constant( "$class::USER_ID"    ) );

        // UserTrait
        $this->assertSame( 'activated'   , constant( "$class::ACTIVATED"      ) );
        $this->assertSame( 'roles'       , constant( "$class::ROLES"          ) );
        $this->assertSame( 'firstLoginAt', constant( "$class::FIRST_LOGIN_AT" ) );
    }
}
