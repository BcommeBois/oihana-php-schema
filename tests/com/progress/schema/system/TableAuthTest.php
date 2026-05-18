<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\Table ;
use com\progress\schema\system\TableAuth ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class TableAuthTest extends TestCase
{
    public function testDefaults(): void
    {
        $auth = new TableAuth();

        $this->assertNull( $auth->alter      ?? null );
        $this->assertNull( $auth->delete     ?? null );
        $this->assertNull( $auth->grantee    ?? null );
        $this->assertNull( $auth->grantor    ?? null );
        $this->assertNull( $auth->index      ?? null );
        $this->assertNull( $auth->insert     ?? null );
        $this->assertNull( $auth->references ?? null );
        $this->assertNull( $auth->select     ?? null );
        $this->assertNull( $auth->table      ?? null );
        $this->assertNull( $auth->tableOwner ?? null );
        $this->assertNull( $auth->update     ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new TableAuth() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , TableAuth::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $auth = new TableAuth
        ([
            'grantee'    => 'alice'       ,
            'grantor'    => 'sysprogress' ,
            'table'      => 'Customer'    ,
            'tableOwner' => 'PUB'         ,
            'select'     => true          ,
            'insert'     => true          ,
            'update'     => false         ,
            'delete'     => false         ,
            'references' => true          ,
            'index'      => false         ,
            'alter'      => false         ,
        ]);

        $this->assertSame( 'alice'       , $auth->grantee    );
        $this->assertSame( 'sysprogress' , $auth->grantor    );
        $this->assertSame( 'Customer'    , $auth->table      );
        $this->assertSame( 'PUB'         , $auth->tableOwner );
        $this->assertTrue (                $auth->select     );
        $this->assertTrue (                $auth->insert     );
        $this->assertFalse(                $auth->update     );
        $this->assertFalse(                $auth->delete     );
        $this->assertTrue (                $auth->references );
        $this->assertFalse(                $auth->index      );
        $this->assertFalse(                $auth->alter      );
    }

    public function testTableCanBeAnObject(): void
    {
        $auth        = new TableAuth();
        $auth->table = new Table([ 'name' => 'Customer' ]);

        $this->assertInstanceOf( Table::class , $auth->table );
    }
}
