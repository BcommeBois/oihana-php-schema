<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\Column ;
use com\progress\schema\system\Table ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class ColumnTest extends TestCase
{
    public function testDefaults(): void
    {
        $column = new Column();

        $this->assertNull( $column->caseSensitive ?? null );
        $this->assertNull( $column->charSet       ?? null );
        $this->assertNull( $column->collation     ?? null );
        $this->assertNull( $column->columnId      ?? null );
        $this->assertNull( $column->columnType    ?? null );
        $this->assertNull( $column->decimal       ?? null );
        $this->assertNull( $column->defaultValue  ?? null );
        $this->assertNull( $column->format        ?? null );
        $this->assertNull( $column->label         ?? null );
        $this->assertNull( $column->mandatory     ?? null );
        $this->assertNull( $column->nullFlag      ?? null );
        $this->assertNull( $column->precision     ?? null );
        $this->assertNull( $column->radix         ?? null );
        $this->assertNull( $column->scale         ?? null );
        $this->assertNull( $column->table         ?? null );
        $this->assertNull( $column->width         ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new Column() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , Column::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $column = new Column
        ([
            'name'          => 'firstName' ,
            'columnId'      => 3           ,
            'columnType'    => 'VARCHAR'   ,
            'charSet'       => 'utf-8'     ,
            'collation'     => 'BASIC'     ,
            'defaultValue'  => ''          ,
            'format'        => 'x(30)'     ,
            'label'         => 'First name',
            'mandatory'     => true        ,
            'nullFlag'      => false       ,
            'precision'     => null        ,
            'radix'         => 10          ,
            'scale'         => 0           ,
            'width'         => 30          ,
            'caseSensitive' => false       ,
            'decimal'       => 0           ,
            'table'         => 'Customer'  ,
        ]);

        $this->assertSame( 'firstName'  , $column->name          );
        $this->assertSame( 3            , $column->columnId      );
        $this->assertSame( 'VARCHAR'    , $column->columnType    );
        $this->assertSame( 'utf-8'      , $column->charSet       );
        $this->assertSame( 'BASIC'      , $column->collation     );
        $this->assertSame( ''           , $column->defaultValue  );
        $this->assertSame( 'x(30)'      , $column->format        );
        $this->assertSame( 'First name' , $column->label         );
        $this->assertTrue (               $column->mandatory     );
        $this->assertFalse(               $column->nullFlag      );
        $this->assertNull (               $column->precision     );
        $this->assertSame( 10           , $column->radix         );
        $this->assertSame( 0            , $column->scale         );
        $this->assertSame( 30           , $column->width         );
        $this->assertFalse(               $column->caseSensitive );
        $this->assertSame( 0            , $column->decimal       );
        $this->assertSame( 'Customer'   , $column->table         );
    }

    public function testTableCanBeAnObject(): void
    {
        $column        = new Column();
        $column->table = new Table([ 'name' => 'Customer' ]);

        $this->assertInstanceOf( Table::class , $column->table );
        $this->assertSame( 'Customer' , $column->table->name );
    }
}
