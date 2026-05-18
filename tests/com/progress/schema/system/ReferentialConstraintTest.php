<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\ReferentialConstraint ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class ReferentialConstraintTest extends TestCase
{
    public function testDefaults(): void
    {
        $ref = new ReferentialConstraint();

        $this->assertNull( $ref->constraintName  ?? null );
        $this->assertNull( $ref->constraintOwner ?? null );
        $this->assertNull( $ref->deleteRule      ?? null );
        $this->assertNull( $ref->matchType       ?? null );
        $this->assertNull( $ref->uniqueName      ?? null );
        $this->assertNull( $ref->uniqueOwner     ?? null );
        $this->assertNull( $ref->updateRule      ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new ReferentialConstraint() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , ReferentialConstraint::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $ref = new ReferentialConstraint
        ([
            'constraintName'  => 'FK_Order_Customer' ,
            'constraintOwner' => 'PUB'               ,
            'uniqueName'      => 'PK_Customer'       ,
            'uniqueOwner'     => 'PUB'               ,
            'matchType'       => 'SIMPLE'            ,
            'updateRule'      => 'C'                 ,
            'deleteRule'      => 'R'                 ,
        ]);

        $this->assertSame( 'FK_Order_Customer' , $ref->constraintName  );
        $this->assertSame( 'PUB'               , $ref->constraintOwner );
        $this->assertSame( 'PK_Customer'       , $ref->uniqueName      );
        $this->assertSame( 'PUB'               , $ref->uniqueOwner     );
        $this->assertSame( 'SIMPLE'            , $ref->matchType       );
        $this->assertSame( 'C'                 , $ref->updateRule      );
        $this->assertSame( 'R'                 , $ref->deleteRule      );
    }
}
