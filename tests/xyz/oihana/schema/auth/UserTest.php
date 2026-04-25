<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Person;

use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\auth\Role;
use xyz\oihana\schema\auth\User;

class UserTest extends TestCase
{
    public function testDefaults(): void
    {
        $user = new User();

        $this->assertNull( $user->activated         ?? null );
        $this->assertNull( $user->appMetadata       ?? null );
        $this->assertNull( $user->applications      ?? null );
        $this->assertNull( $user->blockedFor        ?? null );
        $this->assertNull( $user->devices           ?? null );
        $this->assertNull( $user->firstLoginAt      ?? null );
        $this->assertNull( $user->lastLogin         ?? null );
        $this->assertNull( $user->loginsCount       ?? null );
        $this->assertNull( $user->metadata          ?? null );
        $this->assertNull( $user->pendingEmail      ?? null );
        $this->assertNull( $user->pendingEmailSince ?? null );
        $this->assertNull( $user->permissions       ?? null );
        $this->assertNull( $user->permissionsCount  ?? null );
        $this->assertNull( $user->roles             ?? null );
        $this->assertNull( $user->rolesCount        ?? null );
        $this->assertNull( $user->signedUp          ?? null );
    }

    public function testIsPerson(): void
    {
        $this->assertInstanceOf( Person::class , new User() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , User::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $user = new User
        ([
            'activated'         => true ,
            'firstLoginAt'      => '2026-04-20T08:00:00Z' ,
            'lastLogin'         => '2026-04-24T09:00:00Z' ,
            'loginsCount'       => '5' ,
            'pendingEmail'      => '2026-01-01T00:00:00Z' ,
            'pendingEmailSince' => '2026-01-01T00:00:00Z' ,
            'signedUp'          => '2026-01-01T00:00:00Z' ,
            'appMetadata'       => [ 'roles' => [ 'admin' ] ] ,
            'metadata'          => [ 'theme' => 'dark' ] ,
            'applications'      => [ 'app:1' , 'app:2' ] ,
            'blockedFor'        => [ 'api:legacy' ] ,
            'devices'           => [ 'device-uuid-a' ] ,
            'permissionsCount'  => 1 ,
            'rolesCount'        => 2 ,
        ]);

        $this->assertTrue ( $user->activated );
        $this->assertSame( '2026-04-20T08:00:00Z' , $user->firstLoginAt );
        $this->assertSame( '2026-04-24T09:00:00Z' , $user->lastLogin );
        $this->assertSame( '5' , $user->loginsCount );
        $this->assertSame( '2026-01-01T00:00:00Z' , $user->pendingEmail );
        $this->assertSame( '2026-01-01T00:00:00Z' , $user->pendingEmailSince );
        $this->assertSame( '2026-01-01T00:00:00Z' , $user->signedUp );
        $this->assertSame( [ 'roles' => [ 'admin' ] ] , $user->appMetadata );
        $this->assertSame( [ 'theme' => 'dark' ] , $user->metadata );
        $this->assertSame( [ 'app:1' , 'app:2' ] , $user->applications );
        $this->assertSame( [ 'api:legacy' ] , $user->blockedFor );
        $this->assertSame( [ 'device-uuid-a' ] , $user->devices );
        $this->assertSame( 1 , $user->permissionsCount );
        $this->assertSame( 2 , $user->rolesCount );
    }

    public function testPermissionsAndRolesAssignment(): void
    {
        $user = new User();

        $user->permissions = [ new Permission() ];
        $user->roles       = [ new Role() , new Role() ];

        $this->assertContainsOnlyInstancesOf( Permission::class , $user->permissions );
        $this->assertCount( 1 , $user->permissions );

        $this->assertContainsOnlyInstancesOf( Role::class , $user->roles );
        $this->assertCount( 2 , $user->roles );
    }

    /**
     * @throws ReflectionException
     */
    public function testBlockedForAcceptsString(): void
    {
        $user = new User([ 'blockedFor' => 'api:legacy' ]);
        $this->assertSame( 'api:legacy' , $user->blockedFor );
    }
}
