<?php

namespace tests\org\schema\constants ;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use org\schema\constants\Schema;

/**
 * Guards that every property of every `org\schema` class is reachable through a
 * {@see Schema} constant.
 *
 * Hard-coding a key as a string is the failure the constants exist to prevent :
 * a misspelled one is dropped silently on the way in, and nothing in the class,
 * the payload or the serialized output says so. A property with no constant
 * leaves the caller no choice but to spell it by hand, which is why the coverage
 * is checked rather than documented.
 *
 * The check reads the source tree rather than a list, so a class added later is
 * covered the day it lands — including the properties a class brings in through
 * a composed PHP trait, which are named by that trait's own constants.
 */
class SchemaCoverageTest extends TestCase
{
    /**
     * Machinery rather than vocabulary : these carry no schema.org term, and a
     * constant naming them would say a property exists where none is published.
     */
    private const array INTERNALS =
    [
        'atContext' , 'atType' , 'schemaTypeCache' , '__reflection' , '__shortName' ,
        'DEFAULT_JSON_SERIALIZE_OPTIONS' , 'ALL' , 'CONSTANTS' , 'map' , 'default' ,
    ];

    /**
     * Yields every class declared under `src/org/schema`, constants and helpers
     * aside — the first hold no properties, the second are functions.
     *
     * @return iterable<string, array{0: string}>
     */
    public static function provideClasses(): iterable
    {
        $root     = dirname( __DIR__ , 4 ) . '/src/org/schema' ;
        $iterator = new RecursiveIteratorIterator
        (
            new RecursiveDirectoryIterator( $root , FilesystemIterator::SKIP_DOTS )
        ) ;

        foreach ( $iterator as $file )
        {
            if ( $file->getExtension() !== 'php' )
            {
                continue ;
            }

            $path = $file->getPathname() ;

            if ( str_contains( $path , '/constants/' ) || str_contains( $path , '/helpers/' ) )
            {
                continue ;
            }

            $source = file_get_contents( $path ) ;

            if ( !preg_match( '/^\s*(final\s+|abstract\s+)?class\s+/m' , $source ) )
            {
                continue ;
            }

            $class = 'org\\schema' . str_replace( '/' , '\\' , substr( $path , strlen( $root ) , -4 ) ) ;

            yield $class => [ $class ] ;
        }
    }

    #[DataProvider( 'provideClasses' )]
    public function testEveryPropertyIsReachableThroughAConstant( string $class ): void
    {
        $this->assertTrue( class_exists( $class ) , sprintf( '%s does not load.' , $class ) );

        $values     = array_values( ( new ReflectionClass( Schema::class ) )->getConstants() ) ;
        $reflection = new ReflectionClass( $class ) ;
        $orphans    = [] ;

        foreach ( $reflection->getProperties() as $property )
        {
            $name = $property->getName() ;

            if ( $property->getDeclaringClass()->getName() !== $class ) continue ;
            if ( in_array( $name , self::INTERNALS , true ) )           continue ;
            if ( in_array( $name , $values , true ) )                   continue ;

            $orphans[] = $name ;
        }

        $this->assertSame
        (
            [] ,
            $orphans ,
            sprintf
            (
                'No Schema constant names %s of %s. Add them to the matching trait under org\schema\constants\traits.' ,
                implode( ', ' , $orphans ) ,
                $class
            )
        );
    }
}
