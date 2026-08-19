<?php

namespace tests\org\schema\traits ;

use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

use org\schema\constants\Schema;
use org\schema\CreativeWork;
use org\schema\Organization;
use org\schema\Person;

/**
 * The author of a work is a person or an organization — the one thing the property
 * has to be able to hold.
 */
class CreativeWorkTraitTest extends TestCase
{
    public function testAuthorDefaultsToNull(): void
    {
        $this->assertNull( new CreativeWork()->author ?? null );
    }

    public function testAuthorAcceptsAPerson(): void
    {
        $work = new CreativeWork([ Schema::AUTHOR => new Person([ Schema::NAME => 'Ada Lovelace' ] ) ] );

        $this->assertInstanceOf( Person::class , $work->author       );
        $this->assertSame( 'Ada Lovelace'      , $work->author->name );
    }

    public function testAuthorAcceptsAnOrganization(): void
    {
        $work = new CreativeWork([ Schema::AUTHOR => new Organization([ Schema::NAME => 'Etchea' ] ) ] );

        $this->assertInstanceOf( Organization::class , $work->author       );
        $this->assertSame( 'Etchea'                  , $work->author->name );
    }

    /**
     * A work written by several hands states them all, and a work whose author is
     * only known by name states the name.
     */
    public function testAuthorAcceptsAListAndABareName(): void
    {
        $many = new CreativeWork([ Schema::AUTHOR => [ [ Schema::NAME => 'Ada' ] , [ Schema::NAME => 'Alan' ] ] ] );
        $bare = new CreativeWork([ Schema::AUTHOR => 'Ada Lovelace' ] );

        $this->assertIsArray( $many->author );
        $this->assertCount( 2 , $many->author );
        $this->assertSame( 'Ada Lovelace' , $bare->author );
    }

    /**
     * `author` and `audio` are two unrelated properties, and the type of the first
     * must not name the class of the second — writing a person there would then be
     * a type error, and the property could not hold what it is for.
     */
    public function testAuthorIsNotTypedAsAnAudioObject(): void
    {
        $type = new ReflectionProperty( CreativeWork::class , Schema::AUTHOR )->getType() ;

        $names = $type instanceof ReflectionUnionType
               ? array_map( fn( ReflectionNamedType $part ) => $part->getName() , $type->getTypes() )
               : [ $type?->getName() ] ;

        $this->assertContains( Person::class       , $names );
        $this->assertContains( Organization::class , $names );
        $this->assertNotContains( 'org\schema\creativeWork\medias\AudioObject' , $names );
    }
}
