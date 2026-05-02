<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Thing;

use xyz\oihana\schema\auth\Application;
use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\auth\Policy;

class ApplicationTest extends TestCase
{
    public function testDefaults(): void
    {
        $app = new Application();

        $this->assertNull( $app->allowedIPs       ?? null );
        $this->assertNull( $app->clientId         ?? null );
        $this->assertNull( $app->createdBy        ?? null );
        $this->assertNull( $app->default          ?? null );
        $this->assertNull( $app->disabledAt       ?? null );
        $this->assertNull( $app->disabledBy       ?? null );
        $this->assertNull( $app->disabledReason   ?? null );
        $this->assertNull( $app->expiresAt        ?? null );
        $this->assertNull( $app->lastSeenIP       ?? null );
        $this->assertNull( $app->lastUsedAt       ?? null );
        $this->assertNull( $app->metadata         ?? null );
        $this->assertNull( $app->permissions      ?? null );
        $this->assertNull( $app->permissionsCount ?? null );
        $this->assertNull( $app->policies         ?? null );
        $this->assertNull( $app->policiesCount    ?? null );
    }

    public function testContextConstant(): void
    {
        $this->assertSame('https://schema.oihana.xyz' , Application::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $data =
        [
            'clientId'       => 'client-abc' ,
            'createdBy'      => 'user-42' ,
            'default'        => true ,
            'disabledAt'     => '2026-05-01T08:00:00Z' ,
            'disabledBy'     => 'admin-7' ,
            'disabledReason' => 'security policy violation' ,
            'expiresAt'      => '2026-12-31T23:59:59Z' ,
            'allowedIPs'     => [ '10.0.0.0/8' , '192.168.*' ] ,
            'lastSeenIP'     => '203.0.113.42' ,
            'lastUsedAt'     => '2026-04-24T10:00:00Z' ,
            'metadata'       => [ 'foo' => 'bar' ] ,
        ];

        $app = new Application( $data );

        $this->assertSame ( 'client-abc'                  , $app->clientId       );
        $this->assertSame ( 'user-42'                     , $app->createdBy      );
        $this->assertTrue ( $app->default                                        );
        $this->assertSame ( '2026-05-01T08:00:00Z'        , $app->disabledAt     );
        $this->assertSame ( 'admin-7'                     , $app->disabledBy     );
        $this->assertSame ( 'security policy violation'   , $app->disabledReason );
        $this->assertSame ( '2026-12-31T23:59:59Z'        , $app->expiresAt      );
        $this->assertSame ( [ '10.0.0.0/8' , '192.168.*' ], $app->allowedIPs     );
        $this->assertSame ( '203.0.113.42'                , $app->lastSeenIP     );
        $this->assertSame ( '2026-04-24T10:00:00Z'        , $app->lastUsedAt     );
        $this->assertSame ( [ 'foo' => 'bar' ]            , $app->metadata       );
    }

    public function testCreatedByAndDisabledByAcceptThing(): void
    {
        $creator  = new Thing();
        $disabler = new Thing();

        $app = new Application();

        $app->createdBy  = $creator;
        $app->disabledBy = $disabler;

        $this->assertInstanceOf( Thing::class , $app->createdBy  );
        $this->assertInstanceOf( Thing::class , $app->disabledBy );
        $this->assertSame( $creator  , $app->createdBy  );
        $this->assertSame( $disabler , $app->disabledBy );
    }

    public function testPermissionsAssignment(): void
    {
        $app = new Application();

        $app->permissions = [ new Permission() ];
        $app->permissionsCount = 1;

        $this->assertCount( 1 , $app->permissions );
        $this->assertContainsOnlyInstancesOf( Permission::class , $app->permissions );

        $this->assertSame( 1 , $app->permissionsCount );
    }

    public function testPoliciesAssignment(): void
    {
        $app = new Application();

        $app->policies      = [ new Policy() , new Policy() , new Policy() ];
        $app->policiesCount = 3;

        $this->assertCount( 3 , $app->policies );
        $this->assertContainsOnlyInstancesOf( Policy::class , $app->policies );
        $this->assertSame( 3 , $app->policiesCount );
    }
}
