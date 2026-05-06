<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Thing;

use xyz\oihana\schema\auth\Keyfile;
use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\auth\Policy;
use xyz\oihana\schema\auth\Service;

class ServiceTest extends TestCase
{
    public function testDefaults(): void
    {
        $service = new Service();

        $this->assertNull( $service->allowedIPs       ?? null );
        $this->assertNull( $service->clientId         ?? null );
        $this->assertNull( $service->createdBy        ?? null );
        $this->assertNull( $service->disabledAt       ?? null );
        $this->assertNull( $service->disabledBy       ?? null );
        $this->assertNull( $service->disabledReason   ?? null );
        $this->assertNull( $service->expiresAt        ?? null );
        $this->assertNull( $service->keyId            ?? null );
        $this->assertNull( $service->keyfile          ?? null );
        $this->assertNull( $service->lastSeenIP       ?? null );
        $this->assertNull( $service->lastUsedAt       ?? null );
        $this->assertNull( $service->metadata         ?? null );
        $this->assertNull( $service->permissions      ?? null );
        $this->assertNull( $service->permissionsCount ?? null );
        $this->assertNull( $service->policies         ?? null );
        $this->assertNull( $service->policiesCount    ?? null );
        $this->assertNull( $service->protected        ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new Service() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , Service::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'allowedIPs'     , Service::ALLOWED_IPS     );
        $this->assertSame( 'clientId'       , Service::CLIENT_ID       );
        $this->assertSame( 'createdBy'      , Service::CREATED_BY      );
        $this->assertSame( 'disabledAt'     , Service::DISABLED_AT     );
        $this->assertSame( 'disabledBy'     , Service::DISABLED_BY     );
        $this->assertSame( 'disabledReason' , Service::DISABLED_REASON );
        $this->assertSame( 'expiresAt'      , Service::EXPIRES_AT      );
        $this->assertSame( 'keyId'          , Service::KEY_ID          );
        $this->assertSame( 'keyfile'        , Service::KEYFILE         );
        $this->assertSame( 'lastSeenIP'     , Service::LAST_SEEN_IP    );
        $this->assertSame( 'lastUsedAt'     , Service::LAST_USED_AT    );
        $this->assertSame( 'metadata'       , Service::METADATA        );
        $this->assertSame( 'permissions'    , Service::PERMISSIONS     );
        $this->assertSame( 'protected'      , Service::PROTECTED       );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $data =
        [
            'allowedIPs'     => [ '10.0.0.0/8' , '192.168.*' ]   ,
            'clientId'       => 'service-abc'                    ,
            'createdBy'      => 'user-42'                        ,
            'disabledAt'     => '2026-05-01T08:00:00Z'           ,
            'disabledBy'     => 'admin-7'                        ,
            'disabledReason' => 'security policy violation'      ,
            'expiresAt'      => '2026-12-31T23:59:59Z'           ,
            'keyId'          => '365491048845303185'             ,
            'lastSeenIP'     => '203.0.113.42'                   ,
            'lastUsedAt'     => '2026-04-24T10:00:00Z'           ,
            'metadata'       => [ 'foo' => 'bar' ]               ,
            'protected'      => true                             ,
        ];

        $service = new Service( $data );

        $this->assertSame( [ '10.0.0.0/8' , '192.168.*' ]    , $service->allowedIPs     );
        $this->assertSame( 'service-abc'                     , $service->clientId       );
        $this->assertSame( 'user-42'                         , $service->createdBy      );
        $this->assertSame( '2026-05-01T08:00:00Z'            , $service->disabledAt     );
        $this->assertSame( 'admin-7'                         , $service->disabledBy     );
        $this->assertSame( 'security policy violation'       , $service->disabledReason );
        $this->assertSame( '2026-12-31T23:59:59Z'            , $service->expiresAt      );
        $this->assertSame( '365491048845303185'              , $service->keyId          );
        $this->assertSame( '203.0.113.42'                    , $service->lastSeenIP     );
        $this->assertSame( '2026-04-24T10:00:00Z'            , $service->lastUsedAt     );
        $this->assertSame( [ 'foo' => 'bar' ]                , $service->metadata       );
        $this->assertTrue( $service->protected                                          );
    }

    public function testCreatedByAndDisabledByAcceptThing(): void
    {
        $creator  = new Thing();
        $disabler = new Thing();

        $service = new Service();

        $service->createdBy  = $creator;
        $service->disabledBy = $disabler;

        $this->assertInstanceOf( Thing::class , $service->createdBy  );
        $this->assertInstanceOf( Thing::class , $service->disabledBy );
        $this->assertSame( $creator  , $service->createdBy  );
        $this->assertSame( $disabler , $service->disabledBy );
    }

    public function testKeyfileAssignment(): void
    {
        $service = new Service();

        $keyfile = new Keyfile();
        $keyfile->keyId  = '365491048845303185' ;
        $keyfile->userId = '365491048845434257' ;
        $keyfile->issuer = 'https://my-org.zitadel.cloud' ;

        $service->keyfile = $keyfile;

        $this->assertInstanceOf( Keyfile::class , $service->keyfile );
        $this->assertSame( '365491048845303185' , $service->keyfile->keyId );
    }

    public function testPermissionsAssignment(): void
    {
        $service = new Service();

        $service->permissions      = [ new Permission() ];
        $service->permissionsCount = 1;

        $this->assertCount( 1 , $service->permissions );
        $this->assertContainsOnlyInstancesOf( Permission::class , $service->permissions );
        $this->assertSame( 1 , $service->permissionsCount );
    }

    public function testPoliciesAssignment(): void
    {
        $service = new Service();

        $service->policies      = [ new Policy() , new Policy() , new Policy() ];
        $service->policiesCount = 3;

        $this->assertCount( 3 , $service->policies );
        $this->assertContainsOnlyInstancesOf( Policy::class , $service->policies );
        $this->assertSame( 3 , $service->policiesCount );
    }
}
