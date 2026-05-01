<?php

namespace tests\xyz\oihana\schema\constants\traits ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\AuthTrait;
use xyz\oihana\schema\constants\traits\auth\ApplicationTemplateTrait;
use xyz\oihana\schema\constants\traits\auth\ApplicationTrait;
use xyz\oihana\schema\constants\traits\auth\InvitationTrait;
use xyz\oihana\schema\constants\traits\auth\PermissionTrait;
use xyz\oihana\schema\constants\traits\auth\PolicyTrait;
use xyz\oihana\schema\constants\traits\auth\RoleTrait;
use xyz\oihana\schema\constants\traits\auth\ScopeTrait;
use xyz\oihana\schema\constants\traits\auth\SessionTrait;
use xyz\oihana\schema\constants\traits\auth\UserTrait;
use xyz\oihana\schema\constants\traits\auth\WebAPITrait;
use xyz\oihana\schema\constants\traits\auth\WebApplicationTrait;

class AuthTraitTest extends TestCase
{
    public function testComposition(): void
    {
        $traits = class_uses( AuthTrait::class );

        $this->assertContains( ApplicationTemplateTrait::class , $traits );
        $this->assertContains( ApplicationTrait::class         , $traits );
        $this->assertContains( InvitationTrait::class          , $traits );
        $this->assertContains( PermissionTrait::class          , $traits );
        $this->assertContains( PolicyTrait::class              , $traits );
        $this->assertContains( RoleTrait::class                , $traits );
        $this->assertContains( ScopeTrait::class               , $traits );
        $this->assertContains( SessionTrait::class             , $traits );
        $this->assertContains( UserTrait::class                , $traits );
        $this->assertContains( WebAPITrait::class              , $traits );
        $this->assertContains( WebApplicationTrait::class      , $traits );
    }

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
