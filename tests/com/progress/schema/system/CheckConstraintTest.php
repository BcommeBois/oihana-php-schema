<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\CheckConstraint ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class CheckConstraintTest extends TestCase
{
    public function testDefaults(): void
    {
        $check = new CheckConstraint();

        $this->assertNull( $check->checkText        ?? null );
        $this->assertNull( $check->constraintName   ?? null );
        $this->assertNull( $check->constraintOwner  ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new CheckConstraint() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , CheckConstraint::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $check = new CheckConstraint
        ([
            'constraintName'  => 'CK_Age_Positive' ,
            'constraintOwner' => 'PUB'             ,
            'checkText'       => 'Age >= 0'        ,
        ]);

        $this->assertSame( 'CK_Age_Positive' , $check->constraintName  );
        $this->assertSame( 'PUB'             , $check->constraintOwner );
        $this->assertSame( 'Age >= 0'        , $check->checkText       );
    }
}
