<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\User ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class UserTest extends TestCase
{
    public function testDefaults(): void
    {
        $user = new User();

        $this->assertNull( $user->dbaAccess      ?? null );
        $this->assertNull( $user->grantee        ?? null );
        $this->assertNull( $user->grantor        ?? null );
        $this->assertNull( $user->resourceAccess ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new User() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , User::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $user = new User
        ([
            'grantee'        => 'alice'       ,
            'grantor'        => 'sysprogress' ,
            'dbaAccess'      => true          ,
            'resourceAccess' => true          ,
        ]);

        $this->assertSame( 'alice'       , $user->grantee        );
        $this->assertSame( 'sysprogress' , $user->grantor        );
        $this->assertTrue (                $user->dbaAccess      );
        $this->assertTrue (                $user->resourceAccess );
    }
}
