<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Thing;

use xyz\oihana\schema\auth\OAuthClient;

class OAuthClientTest extends TestCase
{
    public function testDefaults(): void
    {
        $client = new OAuthClient();

        $this->assertNull( $client->appId    ?? null );
        $this->assertNull( $client->clientId ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new OAuthClient() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , OAuthClient::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'appId'    , OAuthClient::APP_ID    );
        $this->assertSame( 'clientId' , OAuthClient::CLIENT_ID );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $client = new OAuthClient
        ([
            OAuthClient::APP_ID    => '365491048845237649' ,
            OAuthClient::CLIENT_ID => 'oidc-client-public' ,
            'name'                 => 'My OAuth Client' ,
        ]);

        $this->assertSame( '365491048845237649'  , $client->appId    );
        $this->assertSame( 'oidc-client-public'  , $client->clientId );
        $this->assertSame( 'My OAuth Client'     , $client->name     );
    }
}
