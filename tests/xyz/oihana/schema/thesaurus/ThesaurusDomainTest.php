<?php

namespace tests\xyz\oihana\schema\thesaurus ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Intangible;
use org\schema\Thing;
use org\schema\constants\Schema;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\thesaurus\ThesaurusDomain;
use xyz\oihana\schema\thesaurus\traits\HasColor;

class ThesaurusDomainTest extends TestCase
{
    public function testDefaults(): void
    {
        $domain = new ThesaurusDomain();

        $this->assertNull( $domain->active ?? null );
        $this->assertNull( $domain->color  ?? null );
        $this->assertNull( $domain->order  ?? null );
    }

    public function testIsIntangible(): void
    {
        $domain = new ThesaurusDomain();

        $this->assertInstanceOf( Intangible::class , $domain );
        $this->assertInstanceOf( Thing::class      , $domain );
    }

    public function testColorComesFromHasColorTrait(): void
    {
        $this->assertContains( HasColor::class , class_uses( ThesaurusDomain::class ) );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA              , ThesaurusDomain::CONTEXT );
        $this->assertSame( 'https://schema.oihana.xyz' , ThesaurusDomain::CONTEXT );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'active' , ThesaurusDomain::ACTIVE );
        $this->assertSame( 'color'  , ThesaurusDomain::COLOR );
        $this->assertSame( 'order'  , ThesaurusDomain::ORDER );
    }

    public function testConstructorCopiesDomainMetadata(): void
    {
        $domain = new ThesaurusDomain
        ([
            'name'                  => 'Products' ,
            ThesaurusDomain::ACTIVE => true ,
            ThesaurusDomain::COLOR  => '#0EA5E9' ,
            ThesaurusDomain::ORDER  => 1 ,
        ]);

        $this->assertSame( 'Products' , $domain->name );
        $this->assertTrue( $domain->active );
        $this->assertSame( '#0EA5E9'  , $domain->color );
        $this->assertSame( 1          , $domain->order );
    }

    /**
     * The i18n-ready labels (`alternateName`, `description`) are inherited
     * from Thing and accept associative language maps.
     */
    public function testInheritedLabelsAcceptLanguageMaps(): void
    {
        $domain = new ThesaurusDomain
        ([
            'name'          => 'Products' ,
            'alternateName' => [ 'en' => 'Products' , 'fr' => 'Produits' ] ,
            'description'   => [ 'en' => 'The product vocabularies.' , 'fr' => 'Les vocabulaires produits.' ] ,
        ]);

        $this->assertSame( 'Produits'                   , $domain->alternateName[ 'fr' ] );
        $this->assertSame( 'The product vocabularies.'  , $domain->description[ 'en' ] );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesDomainMetadata(): void
    {
        $domain = ( new Reflection() )->hydrate
        (
            [
                'name'                  => 'Shipping' ,
                ThesaurusDomain::ACTIVE => false ,
                ThesaurusDomain::ORDER  => 3 ,
            ],
            ThesaurusDomain::class
        );

        $this->assertSame( 'Shipping' , $domain->name );
        $this->assertFalse( $domain->active );
        $this->assertSame( 3 , $domain->order );
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeExposesContextTypeAndMetadata(): void
    {
        $domain = new ThesaurusDomain
        ([
            'name'                  => 'Products' ,
            ThesaurusDomain::ACTIVE => true ,
            ThesaurusDomain::COLOR  => '#0EA5E9' ,
            ThesaurusDomain::ORDER  => 1 ,
        ]);

        $data = $domain->jsonSerialize();

        $this->assertSame( 'ThesaurusDomain'           , $data[ Schema::AT_TYPE ] );
        $this->assertSame( 'https://schema.oihana.xyz' , $data[ Schema::AT_CONTEXT ] );

        $this->assertSame( 'Products' , $data[ 'name' ] );
        $this->assertTrue( $data[ ThesaurusDomain::ACTIVE ] );
        $this->assertSame( '#0EA5E9'  , $data[ ThesaurusDomain::COLOR ] );
        $this->assertSame( 1          , $data[ ThesaurusDomain::ORDER ] );
    }
}
