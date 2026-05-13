<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Thing;

use xyz\oihana\schema\auth\PendingRevocation;

class PendingRevocationTest extends TestCase
{
    public function testDefaults(): void
    {
        $revocation = new PendingRevocation();

        $this->assertNull( $revocation->attempts        ?? null );
        $this->assertNull( $revocation->lastAttemptAt   ?? null );
        $this->assertNull( $revocation->lastError       ?? null );
        $this->assertNull( $revocation->provider        ?? null );
        $this->assertNull( $revocation->reason          ?? null );
        $this->assertNull( $revocation->targetId        ?? null );
        $this->assertNull( $revocation->targetType      ?? null );
        $this->assertNull( $revocation->userIdentifier  ?? null );
        $this->assertNull( $revocation->userKey         ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new PendingRevocation() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , PendingRevocation::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'attempts'       , PendingRevocation::ATTEMPTS        );
        $this->assertSame( 'lastAttemptAt'  , PendingRevocation::LAST_ATTEMPT_AT );
        $this->assertSame( 'lastError'      , PendingRevocation::LAST_ERROR      );
        $this->assertSame( 'provider'       , PendingRevocation::PROVIDER        );
        $this->assertSame( 'reason'         , PendingRevocation::REASON          );
        $this->assertSame( 'targetId'       , PendingRevocation::TARGET_ID       );
        $this->assertSame( 'targetType'     , PendingRevocation::TARGET_TYPE     );
        $this->assertSame( 'userIdentifier' , PendingRevocation::USER_IDENTIFIER );
        $this->assertSame( 'userKey'        , PendingRevocation::USER_KEY        );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $data =
        [
            PendingRevocation::ATTEMPTS        => 2 ,
            PendingRevocation::LAST_ATTEMPT_AT => '2026-04-25T08:00:00Z' ,
            PendingRevocation::LAST_ERROR      => 'connection_timeout' ,
            PendingRevocation::PROVIDER        => 'zitadel' ,
            PendingRevocation::REASON          => 'admin_revoke' ,
            PendingRevocation::TARGET_ID       => 'session-abc-123' ,
            PendingRevocation::TARGET_TYPE     => 'session' ,
            PendingRevocation::USER_IDENTIFIER => 'sub-xyz-789' ,
            PendingRevocation::USER_KEY        => 'user-key-456' ,
        ];

        $revocation = new PendingRevocation( $data );

        $this->assertSame( 2                       , $revocation->attempts       );
        $this->assertSame( '2026-04-25T08:00:00Z'  , $revocation->lastAttemptAt  );
        $this->assertSame( 'connection_timeout'    , $revocation->lastError      );
        $this->assertSame( 'zitadel'               , $revocation->provider       );
        $this->assertSame( 'admin_revoke'          , $revocation->reason         );
        $this->assertSame( 'session-abc-123'       , $revocation->targetId       );
        $this->assertSame( 'session'               , $revocation->targetType     );
        $this->assertSame( 'sub-xyz-789'           , $revocation->userIdentifier );
        $this->assertSame( 'user-key-456'          , $revocation->userKey        );
    }
}
