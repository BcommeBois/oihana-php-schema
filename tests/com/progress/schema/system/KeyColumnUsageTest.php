<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\Column ;
use com\progress\schema\system\KeyColumnUsage ;
use com\progress\schema\system\Table ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class KeyColumnUsageTest extends TestCase
{
    public function testDefaults(): void
    {
        $usage = new KeyColumnUsage();

        $this->assertNull( $usage->column          ?? null );
        $this->assertNull( $usage->constraintName  ?? null );
        $this->assertNull( $usage->constraintOwner ?? null );
        $this->assertNull( $usage->keySequence     ?? null );
        $this->assertNull( $usage->table           ?? null );
        $this->assertNull( $usage->tableOwner      ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new KeyColumnUsage() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , KeyColumnUsage::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $usage = new KeyColumnUsage
        ([
            'constraintName'  => 'PK_Customer' ,
            'constraintOwner' => 'PUB'         ,
            'table'           => 'Customer'    ,
            'tableOwner'      => 'PUB'         ,
            'column'          => 'CustNum'     ,
            'keySequence'     => 1             ,
        ]);

        $this->assertSame( 'PK_Customer' , $usage->constraintName  );
        $this->assertSame( 'PUB'         , $usage->constraintOwner );
        $this->assertSame( 'Customer'    , $usage->table           );
        $this->assertSame( 'PUB'         , $usage->tableOwner      );
        $this->assertSame( 'CustNum'     , $usage->column          );
        $this->assertSame( 1             , $usage->keySequence     );
    }

    public function testTableAndColumnCanBeObjects(): void
    {
        $usage         = new KeyColumnUsage();
        $usage->table  = new Table ([ 'name' => 'Customer' ]);
        $usage->column = new Column([ 'name' => 'CustNum' ]);

        $this->assertInstanceOf( Table::class  , $usage->table  );
        $this->assertInstanceOf( Column::class , $usage->column );
    }
}
