<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\Table ;
use com\progress\schema\system\TableConstraint ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class TableConstraintTest extends TestCase
{
    public function testDefaults(): void
    {
        $constraint = new TableConstraint();

        $this->assertNull( $constraint->constraintOwner ?? null );
        $this->assertNull( $constraint->constraintType  ?? null );
        $this->assertNull( $constraint->deferrability   ?? null );
        $this->assertNull( $constraint->status          ?? null );
        $this->assertNull( $constraint->table           ?? null );
        $this->assertNull( $constraint->tableOwner      ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new TableConstraint() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , TableConstraint::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $constraint = new TableConstraint
        ([
            'name'            => 'PK_Customer' ,
            'constraintOwner' => 'PUB'         ,
            'constraintType'  => 'P'           ,
            'deferrability'   => 'NOT DEFERRABLE' ,
            'status'          => 'A'           ,
            'table'           => 'Customer'    ,
            'tableOwner'      => 'PUB'         ,
        ]);

        $this->assertSame( 'PK_Customer'    , $constraint->name            );
        $this->assertSame( 'PUB'            , $constraint->constraintOwner );
        $this->assertSame( 'P'              , $constraint->constraintType  );
        $this->assertSame( 'NOT DEFERRABLE' , $constraint->deferrability   );
        $this->assertSame( 'A'              , $constraint->status          );
        $this->assertSame( 'Customer'       , $constraint->table           );
        $this->assertSame( 'PUB'            , $constraint->tableOwner      );
    }

    public function testTableCanBeAnObject(): void
    {
        $constraint        = new TableConstraint();
        $constraint->table = new Table([ 'name' => 'Customer' ]);

        $this->assertInstanceOf( Table::class , $constraint->table );
    }
}
