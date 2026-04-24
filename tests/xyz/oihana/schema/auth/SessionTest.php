<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Thing;

use xyz\oihana\schema\auth\Session;

class SessionTest extends TestCase
{
    public function testDefaults(): void
    {
        $session = new Session();

        $this->assertNull( $session->clientId   ?? null );
        $this->assertNull( $session->current    ?? null );
        $this->assertNull( $session->expiresAt  ?? null );
        $this->assertNull( $session->ip         ?? null );
        $this->assertNull( $session->metadata   ?? null );
        $this->assertNull( $session->revokedAt  ?? null );
        $this->assertNull( $session->tokenHash  ?? null );
        $this->assertNull( $session->userAgent  ?? null );
        $this->assertNull( $session->userId     ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new Session() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , Session::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'clientId'  , Session::CLIENT_ID  );
        $this->assertSame( 'current'   , Session::CURRENT    );
        $this->assertSame( 'expiresAt' , Session::EXPIRES_AT );
        $this->assertSame( 'ip'        , Session::IP         );
        $this->assertSame( 'revokedAt' , Session::REVOKED_AT );
        $this->assertSame( 'tokenHash' , Session::TOKEN_HASH );
        $this->assertSame( 'userAgent' , Session::USER_AGENT );
        $this->assertSame( 'userId'    , Session::USER_ID    );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $data =
        [
            Session::CLIENT_ID  => 'azp-client-id' ,
            Session::CURRENT    => true ,
            Session::EXPIRES_AT => '2026-04-25T08:00:00Z' ,
            Session::IP         => '192.168.1.10' ,
            Session::REVOKED_AT => null ,
            Session::TOKEN_HASH => 'sha256-token' ,
            Session::USER_AGENT => 'Mozilla/5.0' ,
            Session::USER_ID    => 'user-key-123' ,
            'metadata'          => [ 'foo' => 'bar' ] ,
        ];

        $session = new Session( $data );

        $this->assertSame( 'azp-client-id'         , $session->clientId  );
        $this->assertTrue ( $session->current );
        $this->assertSame( '2026-04-25T08:00:00Z'  , $session->expiresAt );
        $this->assertSame( '192.168.1.10'          , $session->ip        );
        $this->assertNull( $session->revokedAt );
        $this->assertSame( 'sha256-token'          , $session->tokenHash );
        $this->assertSame( 'Mozilla/5.0'           , $session->userAgent );
        $this->assertSame( 'user-key-123'          , $session->userId    );
        $this->assertSame( [ 'foo' => 'bar' ]      , $session->metadata  );
    }
}
