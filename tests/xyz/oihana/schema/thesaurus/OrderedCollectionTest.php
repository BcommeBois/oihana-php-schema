<?php

namespace tests\xyz\oihana\schema\thesaurus ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use xyz\oihana\schema\thesaurus\Collection;
use xyz\oihana\schema\thesaurus\Concept;
use xyz\oihana\schema\thesaurus\OrderedCollection;

class OrderedCollectionTest extends TestCase
{
    public function testDefaults(): void
    {
        $ordered = new OrderedCollection();

        $this->assertNull( $ordered->member     ?? null );
        $this->assertNull( $ordered->memberList ?? null );
    }

    public function testIsCollection(): void
    {
        $this->assertInstanceOf( Collection::class , new OrderedCollection() );
    }

    public function testContextInheritedFromCollection(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , OrderedCollection::CONTEXT );
    }

    public function testInheritsMemberConstants(): void
    {
        $this->assertSame( 'member'     , OrderedCollection::MEMBER );
        $this->assertSame( 'memberList' , OrderedCollection::MEMBER_LIST );
    }

    /**
     * @throws ReflectionException
     */
    public function testMemberListPreservesOrderAndHydrates(): void
    {
        $ordered = new Reflection()->hydrate
        (
            [
                'name'                         => 'Top sellers' ,
                OrderedCollection::MEMBER_LIST =>
                [
                    [ 'name' => 'Cabernet Sauvignon' , 'termCode' => 'CAB-SAUV' ] ,
                    [ 'name' => 'Merlot'             , 'termCode' => 'MERLOT'   ] ,
                ],
            ],
            OrderedCollection::class
        );

        $this->assertCount( 2 , $ordered->memberList );
        $this->assertInstanceOf( Concept::class , $ordered->memberList[ 0 ] );
        $this->assertSame( 'Cabernet Sauvignon' , $ordered->memberList[ 0 ]->name );
        $this->assertSame( 'Merlot'             , $ordered->memberList[ 1 ]->name );
    }
}
