<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use xyz\oihana\schema\auth\Application;
use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\auth\Policy;
use xyz\oihana\schema\auth\Role;
use xyz\oihana\schema\auth\WebAPI;
use xyz\oihana\schema\constants\CasbinPolicy;
use xyz\oihana\schema\constants\Effect;

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

    public function testToPolicyEmpty(): void
    {
        $this->assertSame( [] , new Policy()->toPolicy() );

        $policy = new Policy();
        $policy->permissions = null;
        $this->assertSame( [] , $policy->toPolicy() );

        $policy->permissions = [];
        $this->assertSame( [] , $policy->toPolicy() );
    }

    public function testToPolicyWithPermissions(): void
    {
        $p1 = new Permission();
        $p1->subject = 'app:billing';
        $p1->domain  = 'api';
        $p1->object  = '/invoices';
        $p1->action  = 'GET';

        $p2 = new Permission();
        $p2->subject = 'app:billing';
        $p2->domain  = 'api';
        $p2->object  = '/invoices';
        $p2->action  = 'POST';

        $policy = new Policy();
        $policy->permissions = [ $p1 , $p2 ];

        $expected =
        [
            [
                CasbinPolicy::SUBJECT => 'app:billing' ,
                CasbinPolicy::DOMAIN  => 'api' ,
                CasbinPolicy::OBJECT  => '/invoices' ,
                CasbinPolicy::ACTION  => 'GET' ,
                CasbinPolicy::EFFECT  => Effect::ALLOW ,
            ],
            [
                CasbinPolicy::SUBJECT => 'app:billing' ,
                CasbinPolicy::DOMAIN  => 'api' ,
                CasbinPolicy::OBJECT  => '/invoices' ,
                CasbinPolicy::ACTION  => 'POST' ,
                CasbinPolicy::EFFECT  => Effect::ALLOW ,
            ],
        ];

        $this->assertSame( $expected , $policy->toPolicy() );
    }

    public function testToCasbinPolicyAlias(): void
    {
        $p = new Permission();
        $p->subject = 'app:billing';
        $p->domain  = 'api';
        $p->object  = '/invoices';
        $p->action  = 'GET';

        $policy = new Policy();
        $policy->permissions = [ $p ];

        $this->assertSame( $policy->toPolicy() , $policy->toCasbinPolicy() );
    }
}
