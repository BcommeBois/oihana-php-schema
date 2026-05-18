<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\Procedure ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class ProcedureTest extends TestCase
{
    public function testDefaults(): void
    {
        $procedure = new Procedure();

        $this->assertNull( $procedure->creator            ?? null );
        $this->assertNull( $procedure->numberOfArguments  ?? null );
        $this->assertNull( $procedure->procedureId        ?? null );
        $this->assertNull( $procedure->procedureOwner     ?? null );
        $this->assertNull( $procedure->procedureText      ?? null );
        $this->assertNull( $procedure->remarks            ?? null );
        $this->assertNull( $procedure->returnType         ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new Procedure() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , Procedure::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $procedure = new Procedure
        ([
            'name'              => 'GetCustomer' ,
            'procedureOwner'    => 'PUB'         ,
            'procedureId'       => 42            ,
            'numberOfArguments' => 1             ,
            'returnType'        => 'VOID'        ,
            'remarks'           => 'Return one customer row by primary key' ,
            'procedureText'     => 'PROCEDURE GetCustomer ...' ,
            'creator'           => 'sysprogress' ,
        ]);

        $this->assertSame( 'GetCustomer' , $procedure->name              );
        $this->assertSame( 'PUB'         , $procedure->procedureOwner    );
        $this->assertSame( 42            , $procedure->procedureId       );
        $this->assertSame( 1             , $procedure->numberOfArguments );
        $this->assertSame( 'VOID'        , $procedure->returnType        );
        $this->assertStringContainsString( 'customer' , $procedure->remarks );
        $this->assertStringContainsString( 'PROCEDURE' , $procedure->procedureText );
        $this->assertSame( 'sysprogress' , $procedure->creator           );
    }
}
