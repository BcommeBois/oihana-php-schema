<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Thing;

use xyz\oihana\schema\auth\Keyfile;
use xyz\oihana\schema\constants\auth\KeyfileType;

class KeyfileTest extends TestCase
{
    public function testDefaults(): void
    {
        $keyfile = new Keyfile();

        $this->assertNull( $keyfile->apiBaseUrl ?? null );
        $this->assertNull( $keyfile->appId      ?? null );
        $this->assertNull( $keyfile->audience   ?? null );
        $this->assertNull( $keyfile->clientId   ?? null );
        $this->assertNull( $keyfile->issuer     ?? null );
        $this->assertNull( $keyfile->key        ?? null );
        $this->assertNull( $keyfile->keyId      ?? null );
        $this->assertNull( $keyfile->scope      ?? null );
        $this->assertNull( $keyfile->type       ?? null );
        $this->assertNull( $keyfile->userId     ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new Keyfile() );
    }

public function testTraitConstants(): void
    {
        $this->assertSame( 'apiBaseUrl' , Keyfile::API_BASE_URL );
        $this->assertSame( 'audience'   , Keyfile::AUDIENCE     );
        $this->assertSame( 'clientId'   , Keyfile::CLIENT_ID    );
        $this->assertSame( 'issuer'     , Keyfile::ISSUER       );
        $this->assertSame( 'key'        , Keyfile::KEY          );
        $this->assertSame( 'keyId'      , Keyfile::KEY_ID       );
        $this->assertSame( 'scope'      , Keyfile::SCOPE        );
        $this->assertSame( 'type'       , Keyfile::TYPE         );
        $this->assertSame( 'userId'     , Keyfile::USER_ID      );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $data =
        [
            Keyfile::API_BASE_URL => 'https://api.example.com'                                                ,
            Keyfile::AUDIENCE     => '365491048845237649'                                                     ,
            Keyfile::CLIENT_ID    => 'service-account-client'                                                 ,
            Keyfile::ISSUER       => 'https://my-org.zitadel.cloud'                                           ,
            Keyfile::KEY          => "-----BEGIN RSA PRIVATE KEY-----\nMIIEpAIBAAKCAQEA...\n-----END RSA PRIVATE KEY-----\n" ,
            Keyfile::KEY_ID       => '365491048845303185'                                                     ,
            Keyfile::SCOPE        => 'openid profile urn:zitadel:iam:org:project:id:365491048845237649:aud'   ,
            Keyfile::TYPE         => KeyfileType::SERVICE_ACCOUNT                                             ,
            Keyfile::USER_ID      => '365491048845434257'                                                     ,
            'appId'               => '365491048845237649'                                                     ,
        ];

        $keyfile = new Keyfile( $data );

        $this->assertSame( 'https://api.example.com'                                              , $keyfile->apiBaseUrl );
        $this->assertSame( '365491048845237649'                                                   , $keyfile->audience   );
        $this->assertSame( 'service-account-client'                                               , $keyfile->clientId   );
        $this->assertSame( 'https://my-org.zitadel.cloud'                                         , $keyfile->issuer     );
        $this->assertStringStartsWith( '-----BEGIN RSA PRIVATE KEY-----'                          , $keyfile->key        );
        $this->assertSame( '365491048845303185'                                                   , $keyfile->keyId      );
        $this->assertSame( 'openid profile urn:zitadel:iam:org:project:id:365491048845237649:aud' , $keyfile->scope      );
        $this->assertSame( KeyfileType::SERVICE_ACCOUNT                                           , $keyfile->type       );
        $this->assertSame( '365491048845434257'                                                   , $keyfile->userId     );
        $this->assertSame( '365491048845237649'                                                   , $keyfile->appId      );
    }
}
