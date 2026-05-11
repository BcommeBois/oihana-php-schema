<?php

namespace tests\xyz\oihana\schema\auth;

use org\schema\actions\UpdateAction;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use ReflectionClass;

use xyz\oihana\schema\auth\PasswordReset;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\auth\PasswordResetTrait;

/**
 * Smoke tests for {@see PasswordReset}.
 *
 * Pins the structural invariants the rest of the auth stack relies on:
 * parent class, mixed trait, exposed constants, declared properties.
 */
#[CoversClass( PasswordReset::class )]
class PasswordResetTest extends TestCase
{
    public function testExtendsUpdateAction() :void
    {
        $this->assertTrue
        (
            is_subclass_of( PasswordReset::class , UpdateAction::class ) ,
            'PasswordReset must extend UpdateAction (Schema.org semantics: update a password).'
        ) ;
    }

    public function testUsesPasswordResetTrait() :void
    {
        $traits = class_uses( PasswordReset::class ) ;

        $this->assertContains
        (
            PasswordResetTrait::class ,
            $traits ,
            'PasswordReset must use PasswordResetTrait to expose property constants.'
        ) ;
    }

    public function testContextConstant() :void
    {
        $this->assertSame( Oihana::SCHEMA , PasswordReset::CONTEXT ) ;
    }

    public function testPropertyConstantsAreDeclared() :void
    {
        $this->assertSame( 'email'       , PasswordReset::EMAIL        ) ;
        $this->assertSame( 'redirectUrl' , PasswordReset::REDIRECT_URL ) ;
        $this->assertSame( 'sentAt'      , PasswordReset::SENT_AT      ) ;
        $this->assertSame( 'token'       , PasswordReset::TOKEN        ) ;
    }

    public function testActionStatusConstantsAreDeclared() :void
    {
        $this->assertSame( 'pending'   , PasswordReset::ACTION_STATUS_PENDING   ) ;
        $this->assertSame( 'consumed'  , PasswordReset::ACTION_STATUS_CONSUMED  ) ;
        $this->assertSame( 'expired'   , PasswordReset::ACTION_STATUS_EXPIRED   ) ;
        $this->assertSame( 'cancelled' , PasswordReset::ACTION_STATUS_CANCELLED ) ;
    }

    public function testDeclaresExpectedPublicProperties() :void
    {
        $ref = new ReflectionClass( PasswordReset::class ) ;

        foreach( [ 'email' , 'redirectUrl' , 'sentAt' , 'token' ] as $name )
        {
            $this->assertTrue
            (
                $ref->hasProperty( $name ) ,
                "PasswordReset must declare a public \$$name property."
            ) ;
        }
    }
}