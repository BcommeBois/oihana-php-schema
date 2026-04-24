<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use xyz\oihana\schema\auth\Application;
use xyz\oihana\schema\auth\ApplicationTemplate;
use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\auth\Scope;
use xyz\oihana\schema\auth\WebAPI;

class ScopeTest extends TestCase
{
    public function testDefaults(): void
    {
        $scope = new Scope();

        $this->assertNull( $scope->applications              ?? null );
        $this->assertNull( $scope->applicationsCount         ?? null );
        $this->assertNull( $scope->applicationTemplates      ?? null );
        $this->assertNull( $scope->applicationTemplatesCount ?? null );
        $this->assertNull( $scope->color                     ?? null );
        $this->assertNull( $scope->permissions               ?? null );
        $this->assertNull( $scope->permissionsCount          ?? null );
        $this->assertNull( $scope->protected                 ?? null );
        $this->assertNull( $scope->system                    ?? null );
    }

    public function testIsWebAPI(): void
    {
        $this->assertInstanceOf( WebAPI::class , new Scope() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , Scope::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $scope = new Scope
        ([
            'color'                     => '#00ff00' ,
            'protected'                 => true ,
            'system'                    => false ,
            'permissionsCount'          => 2 ,
            'applicationsCount'         => 3 ,
            'applicationTemplatesCount' => 4 ,
        ]);

        $this->assertSame( '#00ff00' , $scope->color );
        $this->assertTrue ( $scope->protected );
        $this->assertFalse( $scope->system    );
        $this->assertSame( 2 , $scope->permissionsCount );
        $this->assertSame( 3 , $scope->applicationsCount );
        $this->assertSame( 4 , $scope->applicationTemplatesCount );
    }

    public function testPermissionsAssignment(): void
    {
        $scope = new Scope();

        $scope->permissions = [ new Permission() , new Permission() ];

        $this->assertCount( 2 , $scope->permissions );
        $this->assertContainsOnlyInstancesOf( Permission::class , $scope->permissions );
    }

    public function testApplicationsAssignment(): void
    {
        $scope = new Scope();

        $scope->applications = [ new Application() , new Application() ];

        $this->assertCount( 2 , $scope->applications );
        $this->assertContainsOnlyInstancesOf( Application::class , $scope->applications );
    }

    public function testApplicationTemplatesAssignment(): void
    {
        $scope = new Scope();

        $scope->applicationTemplates = [ new ApplicationTemplate() , new ApplicationTemplate() ];

        $this->assertCount( 2 , $scope->applicationTemplates );
        $this->assertContainsOnlyInstancesOf( ApplicationTemplate::class , $scope->applicationTemplates );
    }
}
