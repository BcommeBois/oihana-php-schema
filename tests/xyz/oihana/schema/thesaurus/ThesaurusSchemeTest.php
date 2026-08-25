<?php

namespace tests\xyz\oihana\schema\thesaurus ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;
use org\schema\creativeWork\DefinedTermSet;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\thesaurus\ConceptScheme;
use xyz\oihana\schema\thesaurus\ThesaurusDomain;
use xyz\oihana\schema\thesaurus\ThesaurusScheme;
use xyz\oihana\schema\traits\HasColor;

class ThesaurusSchemeTest extends TestCase
{
    public function testDefaults(): void
    {
        $scheme = new ThesaurusScheme();

        $this->assertNull( $scheme->active        ?? null );
        $this->assertNull( $scheme->color         ?? null );
        $this->assertNull( $scheme->domain        ?? null );
        $this->assertNull( $scheme->erases        ?? null );
        $this->assertNull( $scheme->harvested     ?? null );
        $this->assertNull( $scheme->order         ?? null );
        $this->assertNull( $scheme->path          ?? null );
        $this->assertNull( $scheme->system        ?? null );
        $this->assertNull( $scheme->writes        ?? null );
        $this->assertNull( $scheme->hasTopConcept ?? null );
    }

    public function testIsConceptScheme(): void
    {
        $scheme = new ThesaurusScheme();

        $this->assertInstanceOf( ConceptScheme::class  , $scheme );
        $this->assertInstanceOf( DefinedTermSet::class , $scheme );
    }

    public function testColorComesFromHasColorTrait(): void
    {
        $this->assertContains( HasColor::class , class_uses( ThesaurusScheme::class ) );
    }

    public function testContextInheritedFromConceptScheme(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , ThesaurusScheme::CONTEXT );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'active'    , ThesaurusScheme::ACTIVE );
        $this->assertSame( 'color'     , ThesaurusScheme::COLOR );
        $this->assertSame( 'domain'    , ThesaurusScheme::DOMAIN );
        $this->assertSame( 'erases'    , ThesaurusScheme::ERASES );
        $this->assertSame( 'harvested' , ThesaurusScheme::HARVESTED );
        $this->assertSame( 'order'     , ThesaurusScheme::ORDER );
        $this->assertSame( 'path'      , ThesaurusScheme::PATH );
        $this->assertSame( 'system'    , ThesaurusScheme::SYSTEM );
        $this->assertSame( 'writes'    , ThesaurusScheme::WRITES );
    }

    public function testInheritsConceptSchemeConstants(): void
    {
        $this->assertSame( 'hasTopConcept' , ThesaurusScheme::HAS_TOP_CONCEPT );
    }

    public function testConstantsAggregatedIntoOihana(): void
    {
        $this->assertSame( 'active'    , Oihana::ACTIVE );
        $this->assertSame( 'domain'    , Oihana::DOMAIN );
        $this->assertSame( 'erases'    , Oihana::ERASES );
        $this->assertSame( 'harvested' , Oihana::HARVESTED );
        $this->assertSame( 'order'     , Oihana::ORDER );
        $this->assertSame( 'path'      , Oihana::PATH );
        $this->assertSame( 'system'    , Oihana::SYSTEM );
        $this->assertSame( 'writes'    , Oihana::WRITES );
    }

    /**
     * The constructor copies the registry metadata verbatim (scalars, a bare
     * domain reference) and leaves a projected domain array raw.
     */
    public function testConstructorCopiesRegistryMetadata(): void
    {
        $scheme = new ThesaurusScheme
        ([
            'name'                     => 'Product categories' ,
            ThesaurusScheme::ACTIVE    => true ,
            ThesaurusScheme::COLOR     => '#22C55E' ,
            ThesaurusScheme::DOMAIN    => 'products' ,
            ThesaurusScheme::HARVESTED => true ,
            ThesaurusScheme::ORDER     => 1 ,
            ThesaurusScheme::PATH      => '/thesaurus/products/categories' ,
            ThesaurusScheme::SYSTEM    => true ,
            ThesaurusScheme::WRITES    => [ 'patch' => [ 'active' , 'color' ] ] ,
            ThesaurusScheme::ERASES    => [ 'patch' => [ 'color' ] ] ,
        ]);

        $this->assertSame( 'Product categories'             , $scheme->name );
        $this->assertTrue( $scheme->active );
        $this->assertSame( '#22C55E'                        , $scheme->color );
        $this->assertSame( 'products'                       , $scheme->domain );
        $this->assertTrue( $scheme->harvested );
        $this->assertSame( 1                                , $scheme->order );
        $this->assertSame( '/thesaurus/products/categories' , $scheme->path );
        $this->assertTrue( $scheme->system );
        $this->assertSame( [ 'patch' => [ 'active' , 'color' ] ] , $scheme->writes );
        $this->assertSame( [ 'patch' => [ 'color' ] ]            , $scheme->erases );
    }

    /**
     * An empty write surface is a statement — « read-only » — and must survive
     * the round trip as an empty array, distinct from the null of « unknown ».
     * Same for an empty erase surface : « writable, but nothing can be taken
     * back » is an answer of its own.
     */
    public function testEmptyWritesStaysAnEmptyArray(): void
    {
        $scheme = new ThesaurusScheme
        ([
            ThesaurusScheme::WRITES => [] ,
            ThesaurusScheme::ERASES => [] ,
        ]);

        $this->assertSame( [] , $scheme->writes );
        $this->assertSame( [] , $scheme->erases );

        $data = (array) $scheme->jsonSerialize() ;

        $this->assertArrayHasKey( ThesaurusScheme::WRITES , $data );
        $this->assertArrayHasKey( ThesaurusScheme::ERASES , $data );
        $this->assertSame( [] , $data[ ThesaurusScheme::WRITES ] );
        $this->assertSame( [] , $data[ ThesaurusScheme::ERASES ] );
    }

    /**
     * 🔑 Taking a value back is not the same permission as writing it : a
     * partial edit drops the nulls it receives, so a field outside `erases`
     * answers a `null` by doing nothing — without an error. The two lists are
     * therefore served side by side, and `erases` is always a subset.
     */
    public function testTheErasableFieldsAreASubsetOfTheWritableOnes(): void
    {
        $scheme = new ThesaurusScheme
        ([
            ThesaurusScheme::WRITES => [ 'patch' => [ 'active' , 'alternateName' , 'color' ] ] ,
            ThesaurusScheme::ERASES => [ 'patch' => [ 'alternateName' , 'color' ] ] ,
        ]);

        $writes = $scheme->writes[ 'patch' ] ?? [] ;
        $erases = $scheme->erases[ 'patch' ] ?? [] ;

        $this->assertNotSame( [] , $erases );
        $this->assertSame( [] , array_diff( $erases , $writes ) );
    }

    public function testDomainViaConstructorIsLeftRaw(): void
    {
        $scheme = new ThesaurusScheme
        ([
            ThesaurusScheme::DOMAIN => [ 'name' => 'Products' ] ,
        ]);

        $this->assertIsArray( $scheme->domain );
    }

    /**
     * @throws ReflectionException
     */
    public function testDomainViaReflectionIsHydratedIntoThesaurusDomain(): void
    {
        $scheme = ( new Reflection() )->hydrate
        (
            [
                'name'                  => 'Product categories' ,
                ThesaurusScheme::DOMAIN =>
                [
                    'name'                  => 'Products' ,
                    ThesaurusDomain::ORDER  => 1 ,
                ],
            ],
            ThesaurusScheme::class
        );

        $this->assertInstanceOf( ThesaurusScheme::class , $scheme );
        $this->assertInstanceOf( ThesaurusDomain::class , $scheme->domain );
        $this->assertSame( 'Products' , $scheme->domain->name );
        $this->assertSame( 1          , $scheme->domain->order );
    }

    /**
     * @throws ReflectionException
     */
    public function testDomainViaReflectionKeepsBareReferenceRaw(): void
    {
        $scheme = ( new Reflection() )->hydrate
        (
            [
                'name'                  => 'Product categories' ,
                ThesaurusScheme::DOMAIN => 'products' ,
            ],
            ThesaurusScheme::class
        );

        $this->assertInstanceOf( ThesaurusScheme::class , $scheme );
        $this->assertSame( 'products' , $scheme->domain );
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeExposesContextTypeAndRegistryMetadata(): void
    {
        $scheme = new ThesaurusScheme
        ([
            'name'                     => 'Product categories' ,
            ThesaurusScheme::ACTIVE    => true ,
            ThesaurusScheme::DOMAIN    => 'products' ,
            ThesaurusScheme::HARVESTED => true ,
            ThesaurusScheme::PATH      => '/thesaurus/products/categories' ,
            ThesaurusScheme::WRITES    => [ 'patch' => [ 'active' , 'color' ] ] ,
        ]);

        $data = $scheme->jsonSerialize();

        $this->assertSame( 'ThesaurusScheme'                , $data[ Schema::AT_TYPE ] );
        $this->assertSame( 'https://schema.oihana.xyz'      , $data[ Schema::AT_CONTEXT ] );

        $this->assertSame( 'Product categories'             , $data[ 'name' ] );
        $this->assertTrue( $data[ ThesaurusScheme::ACTIVE ] );
        $this->assertSame( 'products'                       , $data[ ThesaurusScheme::DOMAIN ] );
        $this->assertTrue( $data[ ThesaurusScheme::HARVESTED ] );
        $this->assertSame( '/thesaurus/products/categories' , $data[ ThesaurusScheme::PATH ] );
        $this->assertSame( [ 'patch' => [ 'active' , 'color' ] ] , $data[ ThesaurusScheme::WRITES ] );
    }
}
