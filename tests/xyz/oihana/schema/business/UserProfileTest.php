<?php

namespace tests\xyz\oihana\schema\business ;

use org\schema\constants\Schema;
use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Intangible;

use xyz\oihana\schema\auth\Role;
use xyz\oihana\schema\business\UserProfile;

class UserProfileTest extends TestCase
{
    public function testDefaults(): void
    {
        $profile = new UserProfile();

        $this->assertNull( $profile->color        ?? null );
        $this->assertNull( $profile->expectedType ?? null );
        $this->assertNull( $profile->role         ?? null );
    }

    public function testIsIntangible(): void
    {
        $this->assertInstanceOf( Intangible::class , new UserProfile() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , UserProfile::CONTEXT );
    }

    public function testPropertyNameConstants(): void
    {
        $this->assertSame( 'color'        , UserProfile::COLOR );
        $this->assertSame( 'expectedType' , UserProfile::EXPECTED_TYPE );
        $this->assertSame( 'role'         , UserProfile::ROLE );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydrationWithScalarRole(): void
    {
        $profile = new UserProfile
        ([
            Schema::NAME               => 'Commercial' ,
            UserProfile::COLOR         => '#22C55E' ,
            UserProfile::ROLE          => 'seller' ,
            UserProfile::EXPECTED_TYPE => 'Seller' ,
        ]);

        $this->assertSame( 'Commercial' , $profile->name );
        $this->assertSame( '#22C55E'    , $profile->color );
        $this->assertSame( 'seller'     , $profile->role );
        $this->assertSame( 'Seller'     , $profile->expectedType );
    }

    public function testRoleObjectAssignment(): void
    {
        $profile = new UserProfile();

        $profile->role = new Role([ 'name' => 'seller' ]);

        $this->assertInstanceOf( Role::class , $profile->role );
        $this->assertSame( 'seller' , $profile->role->name );
    }
}
