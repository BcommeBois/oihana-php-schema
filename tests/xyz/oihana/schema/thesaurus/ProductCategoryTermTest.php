<?php

namespace tests\xyz\oihana\schema\thesaurus ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\DefinedTerm;

use xyz\oihana\schema\thesaurus\Concept;
use xyz\oihana\schema\thesaurus\ProductCategoryTerm;
use xyz\oihana\schema\thesaurus\traits\HasColor;

class ProductCategoryTermTest extends TestCase
{
    public function testDefaults(): void
    {
        $category = new ProductCategoryTerm();

        $this->assertNull( $category->color    ?? null );
        $this->assertNull( $category->broader  ?? null );
        $this->assertNull( $category->narrower ?? null );
    }

    public function testIsConcept(): void
    {
        $category = new ProductCategoryTerm();

        $this->assertInstanceOf( Concept::class     , $category );
        $this->assertInstanceOf( DefinedTerm::class , $category );
    }

    public function testColorComesFromHasColorTrait(): void
    {
        $this->assertContains( HasColor::class , class_uses( ProductCategoryTerm::class ) );
    }

    public function testContextInheritedFromConcept(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , ProductCategoryTerm::CONTEXT );
    }

    public function testInheritsRelationConstants(): void
    {
        $this->assertSame( 'broader'  , ProductCategoryTerm::BROADER );
        $this->assertSame( 'narrower' , ProductCategoryTerm::NARROWER );
        $this->assertSame( 'color'    , ProductCategoryTerm::COLOR );
    }

    /**
     * The constructor copies scalar fields verbatim (color, a bare broader
     * reference) and leaves nested relations raw.
     */
    public function testConstructorCopiesColorAndScalarBroader(): void
    {
        $category = new ProductCategoryTerm
        ([
            'name'                       => 'Red wine' ,
            'termCode'                   => 'RED' ,
            ProductCategoryTerm::COLOR   => '#7B1E3A' ,
            ProductCategoryTerm::BROADER => 'categories/100' ,
        ]);

        $this->assertSame( 'Red wine'       , $category->name );
        $this->assertSame( '#7B1E3A'        , $category->color );
        $this->assertSame( 'categories/100' , $category->broader );
    }

    /**
     * Through the reflection-based hydration path, the children of a colored
     * product category are hydrated as base Concept instances (the inherited
     * `#[HydrateWith(Concept::class)]` is fixed, so subtypes are not preserved).
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesChildrenAsBaseConcept(): void
    {
        $category = ( new Reflection() )->hydrate
        (
            [
                'name'                     => 'Red wine' ,
                ProductCategoryTerm::COLOR => '#7B1E3A' ,
                ProductCategoryTerm::NARROWER =>
                [
                    [ 'name' => 'Cabernet Sauvignon' , 'termCode' => 'CAB-SAUV' ] ,
                ],
            ],
            ProductCategoryTerm::class
        );

        $this->assertSame( '#7B1E3A' , $category->color );
        $this->assertInstanceOf( Concept::class , $category->narrower[ 0 ] );
        $this->assertSame( 'Cabernet Sauvignon' , $category->narrower[ 0 ]->name );
    }
}
