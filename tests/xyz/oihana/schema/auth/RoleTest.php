<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use xyz\oihana\schema\auth\ApplicationTemplate;
use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\auth\Role;
use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\auth\WebAPI;
use xyz\oihana\schema\constants\CasbinPolicy;
use xyz\oihana\schema\constants\Effect;

class RoleTest extends TestCase
{
    public function testDefaults(): void
    {
        $role = new Role();

        $this->assertNull( $role->applicationTemplates      ?? null );
        $this->assertNull( $role->applicationTemplatesCount ?? null );
        $this->assertNull( $role->color                     ?? null );
        $this->assertNull( $role->level                     ?? null );
        $this->assertNull( $role->permissions               ?? null );
        $this->assertNull( $role->permissionsCount          ?? null );
        $this->assertNull( $role->protected                 ?? null );
        $this->assertNull( $role->system                    ?? null );
        $this->assertNull( $role->users                     ?? null );
        $this->assertNull( $role->usersCount                ?? null );
    }

    public function testIsWebAPI(): void
    {
        $this->assertInstanceOf( WebAPI::class , new Role() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , Role::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $role = new Role
        ([
            'color'                     => '#0000ff' ,
            'level'                     => 100 ,
            'protected'                 => true ,
            'system'                    => true ,
            'permissionsCount'          => 1 ,
            'usersCount'                => 2 ,
            'applicationTemplatesCount' => 3 ,
        ]);

        $this->assertSame( '#0000ff' , $role->color );
        $this->assertSame( 100 , $role->level );
        $this->assertTrue( $role->protected );
        $this->assertTrue( $role->system );
        $this->assertSame( 1 , $role->permissionsCount );
        $this->assertSame( 2 , $role->usersCount );
        $this->assertSame( 3 , $role->applicationTemplatesCount );
    }

    public function testCollectionsAssignment(): void
    {
        $role = new Role();

        $role->permissions          = [ new Permission() ];
        $role->users                = [ new User() , new User() ];
        $role->applicationTemplates = [ new ApplicationTemplate() , new ApplicationTemplate() , new ApplicationTemplate() ];

        $this->assertContainsOnlyInstancesOf( Permission::class          , $role->permissions          );
        $this->assertContainsOnlyInstancesOf( User::class                , $role->users                );
        $this->assertContainsOnlyInstancesOf( ApplicationTemplate::class , $role->applicationTemplates );
    }

    public function testToPolicyEmpty(): void
    {
        $this->assertSame( [] , ( new Role() )->toPolicy() );

        $role = new Role();
        $role->permissions = null;
        $this->assertSame( [] , $role->toPolicy() );

        $role->permissions = [];
        $this->assertSame( [] , $role->toPolicy() );
    }

    public function testToPolicyWithPermissions(): void
    {
        $p1 = new Permission();
        $p1->subject = 'role:admin';
        $p1->domain  = 'api';
        $p1->object  = '/a';
        $p1->action  = 'GET';

        $p2 = new Permission();
        $p2->subject = 'role:admin';
        $p2->domain  = 'api';
        $p2->object  = '/b';
        $p2->action  = 'POST';

        $role = new Role();
        $role->permissions = [ $p1 , $p2 ];

        $expected =
        [
            [
                CasbinPolicy::SUBJECT => 'role:admin' ,
                CasbinPolicy::DOMAIN  => 'api' ,
                CasbinPolicy::OBJECT  => '/a' ,
                CasbinPolicy::ACTION  => 'GET' ,
                CasbinPolicy::EFFECT  => Effect::ALLOW ,
            ],
            [
                CasbinPolicy::SUBJECT => 'role:admin' ,
                CasbinPolicy::DOMAIN  => 'api' ,
                CasbinPolicy::OBJECT  => '/b' ,
                CasbinPolicy::ACTION  => 'POST' ,
                CasbinPolicy::EFFECT  => Effect::ALLOW ,
            ],
        ];

        $this->assertSame( $expected , $role->toPolicy() );
    }
}
