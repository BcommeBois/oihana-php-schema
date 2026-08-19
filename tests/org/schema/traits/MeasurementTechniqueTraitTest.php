<?php

namespace tests\org\schema\traits ;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

use org\schema\constants\Schema;
use org\schema\traits\MeasurementTechniqueTrait;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Guards the pair that says how a figure was measured.
 *
 * The two terms were written by hand on every type carrying a measure, and had
 * already drifted apart : `Dataset` and `DataCatalog` accepted the raw row a
 * store hands back, `DataDownload` did not — the same payload went in on one
 * and **threw a TypeError on the other** — and `PropertyValue` typed both as
 * `mixed`, which is to say not at all. They are declared once now, and this test
 * is what keeps the next copy from being written.
 */
class MeasurementTechniqueTraitTest extends TestCase
{
    /**
     * The two terms the trait carries.
     *
     * @return array<string, array{0: string}>
     */
    public static function provideProperties(): array
    {
        return
        [
            Schema::MEASUREMENT_METHOD    => [ Schema::MEASUREMENT_METHOD    ] ,
            Schema::MEASUREMENT_TECHNIQUE => [ Schema::MEASUREMENT_TECHNIQUE ] ,
        ] ;
    }

    /**
     * Yields every `org\schema` class carrying either term, read off the source
     * tree rather than a list : a class added later is guarded the day it lands.
     *
     * @return iterable<string, array{0: string}>
     */
    public static function provideCarriers(): iterable
    {
        $root     = dirname( __DIR__ , 4 ) . '/src/org/schema' ;
        $iterator = new RecursiveIteratorIterator
        (
            new RecursiveDirectoryIterator( $root , FilesystemIterator::SKIP_DOTS )
        ) ;

        foreach ( $iterator as $file )
        {
            if ( $file->getExtension() !== 'php' ) continue ;

            $path = $file->getPathname() ;

            if ( str_contains( $path , '/constants/' ) || str_contains( $path , '/helpers/' ) ) continue ;
            if ( str_contains( $path , '/traits/' ) )                                           continue ;

            $source = file_get_contents( $path ) ;

            if ( !preg_match( '/^\s*(final\s+|abstract\s+)?class\s+/m' , $source ) ) continue ;

            $class = 'org\\schema' . str_replace( '/' , '\\' , substr( $path , strlen( $root ) , -4 ) ) ;

            if ( !class_exists( $class ) )                                       continue ;
            if ( !property_exists( $class , Schema::MEASUREMENT_TECHNIQUE ) &&
                 !property_exists( $class , Schema::MEASUREMENT_METHOD    ) )    continue ;

            yield $class => [ $class ] ;
        }
    }

    /**
     * Every trait a class composes, its own and its parents'.
     *
     * @return array<int, string>
     */
    private static function traitsOf( string $class ): array
    {
        $traits = [] ;

        for ( $reflection = new ReflectionClass( $class ) ; $reflection ; $reflection = $reflection->getParentClass() )
        {
            foreach ( $reflection->getTraitNames() as $trait )
            {
                $traits[] = $trait ;
                $traits   = array_merge( $traits , self::traitsOf( $trait ) ) ;
            }
        }

        return $traits ;
    }

    /**
     * The union a property accepts, as a sorted list of type names.
     *
     * @return array<int, string>
     */
    private static function typesOf( ReflectionProperty $property ): array
    {
        $type  = $property->getType() ;
        $types = $type instanceof ReflectionUnionType ? $type->getTypes() : [ $type ] ;
        $names = [] ;

        foreach ( $types as $one )
        {
            if ( $one instanceof ReflectionNamedType )
            {
                $names[] = $one->getName() ;
            }
        }

        if ( $type?->allowsNull() && !in_array( 'null' , $names , true ) )
        {
            $names[] = 'null' ;
        }

        sort( $names ) ;

        return $names ;
    }

    #[DataProvider( 'provideCarriers' )]
    public function testEveryCarrierTakesThePairFromTheTrait( string $class ): void
    {
        $this->assertContains
        (
            MeasurementTechniqueTrait::class ,
            self::traitsOf( $class ) ,
            sprintf
            (
                '%s names measurementMethod or measurementTechnique without composing MeasurementTechniqueTrait — the copy this trait exists to end.' ,
                $class
            )
        ) ;
    }

    #[DataProvider( 'provideCarriers' )]
    public function testEveryCarrierReadsThePairTheSameWay( string $class ): void
    {
        foreach ( array_keys( self::provideProperties() ) as $property )
        {
            $this->assertSame
            (
                self::typesOf( new ReflectionProperty( MeasurementTechniqueTrait::class , $property ) ) ,
                self::typesOf( new ReflectionProperty( $class , $property ) ) ,
                sprintf( '%s::$%s must accept exactly what the trait accepts.' , $class , $property )
            ) ;
        }
    }

    /**
     * A payload read back from a store holds raw rows, and a repeated value is
     * a list : both are what the drifted copies refused.
     *
     * @param class-string $class
     */
    #[DataProvider( 'provideCarriers' )]
    public function testEveryCarrierAcceptsRawArrayValues( string $class ): void
    {
        foreach ( array_keys( self::provideProperties() ) as $property )
        {
            $item = new $class( [ $property => [ Schema::NAME => 'Survey' ] ] ) ;

            $this->assertIsArray( $item->{ $property } ) ;
        }
    }

    public function testTheTermsAreNamedByTheirOwnConstantsTrait(): void
    {
        $trait = 'org\\schema\\constants\\traits\\MeasurementTechnique' ;

        $this->assertTrue( trait_exists( $trait ) , sprintf( '%s must exist.' , $trait ) ) ;

        $this->assertSame
        (
            [ Schema::MEASUREMENT_METHOD , Schema::MEASUREMENT_TECHNIQUE ] ,
            array_values( ( new ReflectionClass( $trait ) )->getConstants() )
        ) ;
    }
}
