<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\Sequence ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class SequenceTest extends TestCase
{
    public function testDefaults(): void
    {
        $sequence = new Sequence();

        $this->assertNull( $sequence->cycle         ?? null );
        $this->assertNull( $sequence->increment     ?? null );
        $this->assertNull( $sequence->initialValue  ?? null );
        $this->assertNull( $sequence->maxValue      ?? null );
        $this->assertNull( $sequence->minValue      ?? null );
        $this->assertNull( $sequence->sequenceOwner ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new Sequence() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , Sequence::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $sequence = new Sequence
        ([
            'name'          => 'CustNumSeq' ,
            'sequenceOwner' => 'PUB'        ,
            'cycle'         => false        ,
            'increment'     => 1            ,
            'initialValue'  => 1            ,
            'minValue'      => 1            ,
            'maxValue'      => 999_999_999  ,
        ]);

        $this->assertSame( 'CustNumSeq'  , $sequence->name          );
        $this->assertSame( 'PUB'         , $sequence->sequenceOwner );
        $this->assertFalse(                $sequence->cycle         );
        $this->assertSame( 1             , $sequence->increment     );
        $this->assertSame( 1             , $sequence->initialValue  );
        $this->assertSame( 1             , $sequence->minValue      );
        $this->assertSame( 999_999_999   , $sequence->maxValue      );
    }
}
