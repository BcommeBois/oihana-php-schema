<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use xyz\oihana\schema\auth\Application;
use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\auth\Policy;
use xyz\oihana\schema\auth\Role;
use xyz\oihana\schema\auth\WebAPI;

class PolicyTest extends TestCase
{
    public function testDefaults(): void
    {
        $policy = new Policy();

        $this->assertNull( $policy->applications      ?? null );
        $this->assertNull( $policy->applicationsCount ?? null );
        $this->assertNull( $policy->color             ?? null );
        $this->assertNull( $policy->permissions       ?? null );
        $this->assertNull( $policy->permissionsCount  ?? null );
        $this->assertNull( $policy->protected         ?? null );
        $this->assertNull( $policy->roles             ?? null );
        $this->assertNull( $policy->rolesCount        ?? null );
        $this->assertNull( $policy->system            ?? null );
    }

    public function testIsWebAPI(): void
    {
        $this->assertInstanceOf( WebAPI::class , new Policy() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , Policy::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $policy = new Policy
        ([
            'color'             => '#ff00aa' ,
            'protected'         => true ,
            'system'            => false ,
            'permissionsCount'  => 5 ,
            'applicationsCount' => 7 ,
            'rolesCount'        => 2 ,
        ]);

        $this->assertSame ( '#ff00aa' , $policy->color );
        $this->assertTrue ( $policy->protected );
        $this->assertFalse( $policy->system    );
        $this->assertSame ( 5 , $policy->permissionsCount  );
        $this->assertSame ( 7 , $policy->applicationsCount );
        $this->assertSame ( 2 , $policy->rolesCount        );
    }

    public function testPermissionsAssignment(): void
    {
        $policy = new Policy();

        $policy->permissions = [ new Permission() , new Permission() ];

        $this->assertCount( 2 , $policy->permissions );
        $this->assertContainsOnlyInstancesOf( Permission::class , $policy->permissions );
    }

    public function testApplicationsAssignment(): void
    {
        $policy = new Policy();

        $policy->applications = [ new Application() , new Application() ];

        $this->assertCount( 2 , $policy->applications );
        $this->assertContainsOnlyInstancesOf( Application::class , $policy->applications );
    }

    public function testRolesAssignment(): void
    {
        $policy = new Policy();

        $policy->roles = [ new Role() , new Role() , new Role() ];

        $this->assertCount( 3 , $policy->roles );
        $this->assertContainsOnlyInstancesOf( Role::class , $policy->roles );
    }
}
