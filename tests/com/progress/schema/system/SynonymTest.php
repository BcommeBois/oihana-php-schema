<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\Synonym ;
use com\progress\schema\system\Table ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class SynonymTest extends TestCase
{
    public function testDefaults(): void
    {
        $synonym = new Synonym();

        $this->assertNull( $synonym->baseTable      ?? null );
        $this->assertNull( $synonym->baseTableOwner ?? null );
        $this->assertNull( $synonym->creator        ?? null );
        $this->assertNull( $synonym->synonymOwner   ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new Synonym() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , Synonym::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $synonym = new Synonym
        ([
            'name'           => 'Cust'        ,
            'baseTable'      => 'Customer'    ,
            'baseTableOwner' => 'PUB'         ,
            'synonymOwner'   => 'PUB'         ,
            'creator'        => 'sysprogress' ,
        ]);

        $this->assertSame( 'Cust'        , $synonym->name           );
        $this->assertSame( 'Customer'    , $synonym->baseTable      );
        $this->assertSame( 'PUB'         , $synonym->baseTableOwner );
        $this->assertSame( 'PUB'         , $synonym->synonymOwner   );
        $this->assertSame( 'sysprogress' , $synonym->creator        );
    }

    public function testBaseTableCanBeAnObject(): void
    {
        $synonym            = new Synonym();
        $synonym->baseTable = new Table([ 'name' => 'Customer' ]);

        $this->assertInstanceOf( Table::class , $synonym->baseTable );
    }
}
