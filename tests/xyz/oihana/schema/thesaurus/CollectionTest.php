<?php

namespace tests\xyz\oihana\schema\thesaurus ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Intangible;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\thesaurus\Collection;
use xyz\oihana\schema\thesaurus\Concept;

class CollectionTest extends TestCase
{
    public function testDefaults(): void
    {
        $collection = new Collection();

        $this->assertNull( $collection->member ?? null );
    }

    public function testIsIntangible(): void
    {
        $this->assertInstanceOf( Intangible::class , new Collection() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , Collection::CONTEXT );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'member'     , Collection::MEMBER );
        $this->assertSame( 'memberList' , Collection::MEMBER_LIST );
    }

    public function testConstantsAggregatedIntoOihana(): void
    {
        $this->assertSame( 'member'     , Oihana::MEMBER );
        $this->assertSame( 'memberList' , Oihana::MEMBER_LIST );
    }

    public function testMemberViaConstructorIsLeftRaw(): void
    {
        $collection = new Collection
        ([
            Collection::MEMBER => [ [ 'name' => 'Merlot' , 'termCode' => 'MERLOT' ] ] ,
        ]);

        $this->assertIsArray( $collection->member[ 0 ] );
    }

    /**
     * Without a discriminator, a projected member scores as the fallback
     * Concept (the first HydrateWith class).
     *
     * @throws ReflectionException
     */
    public function testMemberViaReflectionHydratesConcepts(): void
    {
        $collection = new Reflection()->hydrate
        (
            [
                'name'             => 'Featured grape varieties' ,
                Collection::MEMBER =>
                [
                    [ 'name' => 'Cabernet Sauvignon' , 'termCode' => 'CAB-SAUV' ] ,
                    [ 'name' => 'Merlot'             , 'termCode' => 'MERLOT'   ] ,
                ],
            ],
            Collection::class
        );

        $this->assertCount( 2 , $collection->member );
        $this->assertInstanceOf( Concept::class , $collection->member[ 0 ] );
        $this->assertSame( 'Cabernet Sauvignon' , $collection->member[ 0 ]->name );
    }

    /**
     * With an `@type` discriminator, members dispatch polymorphically — a
     * nested collection hydrates as Collection, a plain term as Concept.
     *
     * @throws ReflectionException
     */
    public function testMemberPolymorphicViaDiscriminator(): void
    {
        $collection = new Reflection()->hydrate
        (
            [
                Collection::MEMBER =>
                [
                    [ '@type' => 'Concept'    , 'name' => 'Merlot' ] ,
                    [ '@type' => 'Collection' , 'name' => 'Sub-collection' ] ,
                ],
            ],
            Collection::class
        );

        $this->assertInstanceOf( Concept::class    , $collection->member[ 0 ] );
        $this->assertInstanceOf( Collection::class , $collection->member[ 1 ] );
        $this->assertSame( 'Sub-collection' , $collection->member[ 1 ]->name );
    }
}
