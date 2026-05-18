<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\Column ;
use com\progress\schema\system\ColumnAuth ;
use com\progress\schema\system\Table ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class ColumnAuthTest extends TestCase
{
    public function testDefaults(): void
    {
        $auth = new ColumnAuth();

        $this->assertNull( $auth->column     ?? null );
        $this->assertNull( $auth->grantee    ?? null );
        $this->assertNull( $auth->grantor    ?? null );
        $this->assertNull( $auth->references ?? null );
        $this->assertNull( $auth->select     ?? null );
        $this->assertNull( $auth->table      ?? null );
        $this->assertNull( $auth->tableOwner ?? null );
        $this->assertNull( $auth->update     ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new ColumnAuth() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , ColumnAuth::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $auth = new ColumnAuth
        ([
            'grantee'    => 'alice'       ,
            'grantor'    => 'sysprogress' ,
            'table'      => 'Customer'    ,
            'tableOwner' => 'PUB'         ,
            'column'     => 'Email'       ,
            'select'     => true          ,
            'update'     => false         ,
            'references' => false         ,
        ]);

        $this->assertSame( 'alice'       , $auth->grantee    );
        $this->assertSame( 'sysprogress' , $auth->grantor    );
        $this->assertSame( 'Customer'    , $auth->table      );
        $this->assertSame( 'PUB'         , $auth->tableOwner );
        $this->assertSame( 'Email'       , $auth->column     );
        $this->assertTrue (                $auth->select     );
        $this->assertFalse(                $auth->update     );
        $this->assertFalse(                $auth->references );
    }

    public function testTableAndColumnCanBeObjects(): void
    {
        $auth         = new ColumnAuth();
        $auth->table  = new Table ([ 'name' => 'Customer' ]);
        $auth->column = new Column([ 'name' => 'Email' ]);

        $this->assertInstanceOf( Table::class  , $auth->table  );
        $this->assertInstanceOf( Column::class , $auth->column );
    }
}
