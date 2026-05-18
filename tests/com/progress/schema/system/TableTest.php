<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\Column ;
use com\progress\schema\system\Table ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class TableTest extends TestCase
{
    public function testDefaults(): void
    {
        $table = new Table();

        $this->assertNull( $table->columns         ?? null );
        $this->assertNull( $table->creator         ?? null );
        $this->assertNull( $table->numberOfRows    ?? null );
        $this->assertNull( $table->owner           ?? null );
        $this->assertNull( $table->percentTouched  ?? null );
        $this->assertNull( $table->recordSize      ?? null );
        $this->assertNull( $table->status          ?? null );
        $this->assertNull( $table->tableAttributes ?? null );
        $this->assertNull( $table->type            ?? null );
        $this->assertNull( $table->updateStats     ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new Table() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , Table::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $table = new Table
        ([
            'name'            => 'Customer'    ,
            'owner'           => 'PUB'         ,
            'creator'         => 'sysprogress' ,
            'type'            => 'T'           ,
            'status'          => 'A'           ,
            'numberOfRows'    => 1024          ,
            'percentTouched'  => 42            ,
            'recordSize'      => 96            ,
            'tableAttributes' => 0             ,
            'updateStats'     => '2026-05-18T08:00:00Z' ,
        ]);

        $this->assertSame( 'Customer'             , $table->name            );
        $this->assertSame( 'PUB'                  , $table->owner           );
        $this->assertSame( 'sysprogress'          , $table->creator         );
        $this->assertSame( 'T'                    , $table->type            );
        $this->assertSame( 'A'                    , $table->status          );
        $this->assertSame( 1024                   , $table->numberOfRows    );
        $this->assertSame( 42                     , $table->percentTouched  );
        $this->assertSame( 96                     , $table->recordSize      );
        $this->assertSame( 0                      , $table->tableAttributes );
        $this->assertSame( '2026-05-18T08:00:00Z' , $table->updateStats     );
    }

    public function testColumnsAssignment(): void
    {
        $table          = new Table();
        $table->columns = [ new Column() , new Column() ];

        $this->assertContainsOnlyInstancesOf( Column::class , $table->columns );
        $this->assertCount( 2 , $table->columns );
    }
}
