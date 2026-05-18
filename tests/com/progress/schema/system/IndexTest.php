<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\Column ;
use com\progress\schema\system\Index ;
use com\progress\schema\system\Table ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class IndexTest extends TestCase
{
    public function testDefaults(): void
    {
        $index = new Index();

        $this->assertNull( $index->abbreviate         ?? null );
        $this->assertNull( $index->ascDesc            ?? null );
        $this->assertNull( $index->column             ?? null );
        $this->assertNull( $index->creator            ?? null );
        $this->assertNull( $index->fieldNumber        ?? null );
        $this->assertNull( $index->indexOwner         ?? null );
        $this->assertNull( $index->indexSequence      ?? null );
        $this->assertNull( $index->indexType          ?? null );
        $this->assertNull( $index->numberOfComponents ?? null );
        $this->assertNull( $index->primary            ?? null );
        $this->assertNull( $index->table              ?? null );
        $this->assertNull( $index->unique             ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new Index() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , Index::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $index = new Index
        ([
            'name'               => 'CustomerPK'  ,
            'abbreviate'         => false         ,
            'ascDesc'            => 'A'           ,
            'creator'            => 'sysprogress' ,
            'fieldNumber'        => 1             ,
            'indexOwner'         => 'PUB'         ,
            'indexSequence'      => 0             ,
            'indexType'          => 'U'           ,
            'numberOfComponents' => 1             ,
            'primary'            => true          ,
            'column'             => 'CustNum'     ,
            'table'              => 'Customer'    ,
            'unique'             => true          ,
        ]);

        $this->assertSame( 'CustomerPK'  , $index->name               );
        $this->assertFalse(                $index->abbreviate         );
        $this->assertSame( 'A'           , $index->ascDesc            );
        $this->assertSame( 'sysprogress' , $index->creator            );
        $this->assertSame( 1             , $index->fieldNumber        );
        $this->assertSame( 'PUB'         , $index->indexOwner         );
        $this->assertSame( 0             , $index->indexSequence      );
        $this->assertSame( 'U'           , $index->indexType          );
        $this->assertSame( 1             , $index->numberOfComponents );
        $this->assertTrue (                $index->primary            );
        $this->assertSame( 'CustNum'     , $index->column             );
        $this->assertSame( 'Customer'    , $index->table              );
        $this->assertTrue (                $index->unique             );
    }

    public function testColumnAndTableCanBeObjects(): void
    {
        $index         = new Index();
        $index->column = new Column([ 'name' => 'CustNum' ]);
        $index->table  = new Table ([ 'name' => 'Customer' ]);

        $this->assertInstanceOf( Column::class , $index->column );
        $this->assertInstanceOf( Table::class  , $index->table  );
    }
}
