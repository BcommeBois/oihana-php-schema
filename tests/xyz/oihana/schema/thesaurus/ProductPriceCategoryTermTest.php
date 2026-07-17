<?php

namespace tests\xyz\oihana\schema\thesaurus ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\DefinedTerm;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\thesaurus\Concept;
use xyz\oihana\schema\thesaurus\ProductPriceCategoryTerm;
use xyz\oihana\schema\thesaurus\traits\HasTreeMetrics;
use xyz\oihana\schema\traits\HasColor;

class ProductPriceCategoryTermTest extends TestCase
{
    public function testDefaults(): void
    {
        $priceCategory = new ProductPriceCategoryTerm();

        $this->assertNull( $priceCategory->color         ?? null );
        $this->assertNull( $priceCategory->broader       ?? null );
        $this->assertNull( $priceCategory->narrower      ?? null );
        $this->assertNull( $priceCategory->childrenCount ?? null );
    }

    public function testIsConcept(): void
    {
        $priceCategory = new ProductPriceCategoryTerm();

        $this->assertInstanceOf( Concept::class     , $priceCategory );
        $this->assertInstanceOf( DefinedTerm::class , $priceCategory );
    }

    public function testColorComesFromHasColorTrait(): void
    {
        $this->assertContains( HasColor::class , class_uses( ProductPriceCategoryTerm::class ) );
    }

    public function testChildrenCountComesFromHasTreeMetricsTrait(): void
    {
        $this->assertContains( HasTreeMetrics::class , class_uses( ProductPriceCategoryTerm::class ) );
    }

    public function testContextInheritedFromConcept(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , ProductPriceCategoryTerm::CONTEXT );
    }

    public function testInheritsRelationConstants(): void
    {
        $this->assertSame( 'broader'  , ProductPriceCategoryTerm::BROADER );
        $this->assertSame( 'narrower' , ProductPriceCategoryTerm::NARROWER );
        $this->assertSame( 'color'    , ProductPriceCategoryTerm::COLOR );
    }

    public function testChildrenCountConstant(): void
    {
        $this->assertSame( 'childrenCount' , ProductPriceCategoryTerm::CHILDREN_COUNT );
    }

    public function testChildrenCountAggregatedIntoOihana(): void
    {
        $this->assertSame( 'childrenCount' , Oihana::CHILDREN_COUNT );
    }

    /**
     * A product price category is a distinct type from a product (catalog)
     * category : it must not advertise itself as a catalog category.
     */
    public function testIsNotAProductCategoryTerm(): void
    {
        $priceCategory = new ProductPriceCategoryTerm();

        $this->assertNotInstanceOf( \xyz\oihana\schema\thesaurus\ProductCategoryTerm::class , $priceCategory );
    }

    /**
     * The constructor copies scalar fields verbatim (color, a bare broader
     * reference) and leaves nested relations raw.
     */
    public function testConstructorCopiesColorAndScalarBroader(): void
    {
        $priceCategory = new ProductPriceCategoryTerm
        ([
            'name'                            => 'Premium tier' ,
            'termCode'                        => 'PREM' ,
            ProductPriceCategoryTerm::COLOR   => '#2E5E4E' ,
            ProductPriceCategoryTerm::BROADER => 'priceCategories/100' ,
        ]);

        $this->assertSame( 'Premium tier'        , $priceCategory->name );
        $this->assertSame( '#2E5E4E'             , $priceCategory->color );
        $this->assertSame( 'priceCategories/100' , $priceCategory->broader );
    }

    /**
     * The constructor copies the non-SKOS structural `childrenCount` metric
     * verbatim.
     */
    public function testConstructorCopiesChildrenCount(): void
    {
        $priceCategory = new ProductPriceCategoryTerm
        ([
            'name'                                   => 'Premium tier' ,
            ProductPriceCategoryTerm::CHILDREN_COUNT => 8 ,
        ]);

        $this->assertSame( 8 , $priceCategory->childrenCount );
    }

    /**
     * Through the reflection-based hydration path, `childrenCount` is hydrated
     * as a plain int — a value of `0` marks a leaf (`isLeaf` ⟺ `childrenCount === 0`).
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesLeafChildrenCount(): void
    {
        $priceCategory = ( new Reflection() )->hydrate
        (
            [
                'name'                                   => 'Premium tier' ,
                ProductPriceCategoryTerm::CHILDREN_COUNT => 0 ,
            ],
            ProductPriceCategoryTerm::class
        );

        $this->assertSame( 0 , $priceCategory->childrenCount );
    }

    /**
     * Through the reflection-based hydration path, the children of a colored
     * product price category are hydrated as base Concept instances (the
     * inherited `#[HydrateWith(Concept::class)]` is fixed, so subtypes are not
     * preserved).
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesChildrenAsBaseConcept(): void
    {
        $priceCategory = ( new Reflection() )->hydrate
        (
            [
                'name'                          => 'Premium tier' ,
                ProductPriceCategoryTerm::COLOR => '#2E5E4E' ,
                ProductPriceCategoryTerm::NARROWER =>
                [
                    [ 'name' => 'Gold' , 'termCode' => 'GOLD' ] ,
                ],
            ],
            ProductPriceCategoryTerm::class
        );

        $this->assertSame( '#2E5E4E' , $priceCategory->color );
        $this->assertInstanceOf( Concept::class , $priceCategory->narrower[ 0 ] );
        $this->assertSame( 'Gold' , $priceCategory->narrower[ 0 ]->name );
    }
}
