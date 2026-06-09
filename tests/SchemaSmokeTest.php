<?php

namespace tests ;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

use org\schema\Thing;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Structural smoke test over every class shipped under src/.
 *
 * The bulk of this library is declarative (schema.org entities are mostly typed
 * properties and constants), so line coverage barely touches it. This test
 * guards the structural integrity the coverage number cannot see:
 *
 * - every class file loads without a fatal error (trait composition conflicts,
 *   PSR-4 case mismatches on case-sensitive filesystems, missing parents);
 * - every {@see Thing} subclass instantiates with no arguments, serializes to
 *   an array, and exposes a well-formed schema type URI.
 *
 * It does NOT verify the *values* of the hundreds of constant strings (a typo
 * such as 'nme' instead of 'name' is undetectable without a schema.org
 * reference list) — only that the code is sound enough to load and run.
 */
class SchemaSmokeTest extends TestCase
{
    /**
     * Yields the fully-qualified name of every class declared under src/.
     *
     * The FQCN is derived from the file path, which the PSR-4 autoload roots
     * (`com\progress\`, `org\schema\`, `xyz\oihana\`) map one-to-one onto src/.
     *
     * @return iterable<string, array{0: string}>
     */
    public static function provideClasses(): iterable
    {
        $root     = dirname( __DIR__ ) . '/src' ;
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

            $source = file_get_contents( $file->getPathname() ) ;

            // Keep class files only — skip traits, which are not loadable on their own.
            if ( !preg_match( '/^\s*(final\s+|abstract\s+)?class\s+/m' , $source ) )
            {
                continue ;
            }

            $relative = substr( $file->getPathname() , strlen( $root ) + 1 , -4 ) ;
            $fqcn     = str_replace( '/' , '\\' , $relative ) ;

            yield $fqcn => [ $fqcn ] ;
        }
    }

    #[DataProvider( 'provideClasses' )]
    public function testClassLoadsAndSerializes( string $fqcn ): void
    {
        $this->assertTrue
        (
            class_exists( $fqcn ) ,
            "Class {$fqcn} failed to load (fatal composition, missing parent or PSR-4 case mismatch)."
        ) ;

        $reflection = new ReflectionClass( $fqcn ) ;

        if ( !$reflection->isSubclassOf( Thing::class ) || !$reflection->isInstantiable() )
        {
            return ; // Constant bags and helpers: loading them is the whole assertion.
        }

        $instance = new $fqcn() ;

        $this->assertIsArray
        (
            $instance->jsonSerialize() ,
            "{$fqcn}::jsonSerialize() did not return an array."
        ) ;

        $schemaType = $fqcn::getSchemaType() ;

        $this->assertNotEmpty( $schemaType , "{$fqcn}::getSchemaType() is empty." ) ;
        $this->assertStringContainsString
        (
            '://' ,
            $schemaType ,
            "{$fqcn}::getSchemaType() is not a URI: {$schemaType}"
        ) ;
    }
}
