<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\Table ;
use com\progress\schema\system\Trigger ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class TriggerTest extends TestCase
{
    public function testDefaults(): void
    {
        $trigger = new Trigger();

        $this->assertNull( $trigger->creator      ?? null );
        $this->assertNull( $trigger->event        ?? null );
        $this->assertNull( $trigger->forEach      ?? null );
        $this->assertNull( $trigger->table        ?? null );
        $this->assertNull( $trigger->timing       ?? null );
        $this->assertNull( $trigger->triggerOwner ?? null );
        $this->assertNull( $trigger->triggerText  ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new Trigger() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , Trigger::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $trigger = new Trigger
        ([
            'name'         => 'OnInsertCustomer' ,
            'triggerOwner' => 'PUB'              ,
            'table'        => 'Customer'         ,
            'event'        => 'I'                ,
            'forEach'      => 'R'                ,
            'timing'       => 'B'                ,
            'creator'      => 'sysprogress'      ,
            'triggerText'  => 'BEGIN ... END'    ,
        ]);

        $this->assertSame( 'OnInsertCustomer' , $trigger->name         );
        $this->assertSame( 'PUB'              , $trigger->triggerOwner );
        $this->assertSame( 'Customer'         , $trigger->table        );
        $this->assertSame( 'I'                , $trigger->event        );
        $this->assertSame( 'R'                , $trigger->forEach      );
        $this->assertSame( 'B'                , $trigger->timing       );
        $this->assertSame( 'sysprogress'      , $trigger->creator      );
        $this->assertSame( 'BEGIN ... END'    , $trigger->triggerText  );
    }

    public function testTableCanBeAnObject(): void
    {
        $trigger        = new Trigger();
        $trigger->table = new Table([ 'name' => 'Customer' ]);

        $this->assertInstanceOf( Table::class , $trigger->table );
    }
}
