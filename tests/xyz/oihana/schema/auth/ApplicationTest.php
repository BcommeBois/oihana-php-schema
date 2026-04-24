<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use xyz\oihana\schema\auth\Application;
use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\auth\Scope;

class ApplicationTest extends TestCase
{
    public function testDefaults(): void
    {
        $app = new Application();

        $this->assertNull( $app->clientId         ?? null );
        $this->assertNull( $app->default          ?? null );
        $this->assertNull( $app->expiresAt        ?? null );
        $this->assertNull( $app->allowedIPs       ?? null );
        $this->assertNull( $app->lastUsedAt       ?? null );
        $this->assertNull( $app->metadata         ?? null );
        $this->assertNull( $app->scopes           ?? null );
        $this->assertNull( $app->scopesCount      ?? null );
        $this->assertNull( $app->permissions      ?? null );
        $this->assertNull( $app->permissionsCount ?? null );
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
            'clientId'   => 'client-abc' ,
            'default'    => true ,
            'expiresAt'  => '2026-12-31T23:59:59Z' ,
            'allowedIPs' => [ '10.0.0.0/8' , '192.168.*' ] ,
            'lastUsedAt' => '2026-04-24T10:00:00Z' ,
            'metadata'   => [ 'foo' => 'bar' ] ,
        ];

        $app = new Application( $data );

        $this->assertSame( 'client-abc'             , $app->clientId   );
        $this->assertTrue ( $app->default );
        $this->assertSame( '2026-12-31T23:59:59Z'   , $app->expiresAt  );
        $this->assertSame( [ '10.0.0.0/8' , '192.168.*' ] , $app->allowedIPs );
        $this->assertSame( '2026-04-24T10:00:00Z'   , $app->lastUsedAt );
        $this->assertSame( [ 'foo' => 'bar' ]       , $app->metadata   );
    }

    /**
     * @throws ReflectionException
     */
    public function testScopesAndPermissionsAssignment(): void
    {
        $app = new Application();

        $app->scopes = [ new Scope() , new Scope() ];
        $app->permissions = [ new Permission() ];
        $app->scopesCount = 2;
        $app->permissionsCount = 1;

        $this->assertCount( 2 , $app->scopes );
        $this->assertContainsOnlyInstancesOf( Scope::class , $app->scopes );

        $this->assertCount( 1 , $app->permissions );
        $this->assertContainsOnlyInstancesOf( Permission::class , $app->permissions );

        $this->assertSame( 2 , $app->scopesCount );
        $this->assertSame( 1 , $app->permissionsCount );
    }
}
