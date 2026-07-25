<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\DefinedTerm;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\products\ProductType;
use xyz\oihana\schema\traits\HasColor;

class ProductTypeTest extends TestCase
{
    public function testDefaults(): void
    {
        $type = new ProductType();

        $this->assertNull( $type->color     ?? null );
        $this->assertNull( $type->stockable ?? null );
        $this->assertNull( $type->trackable ?? null );
    }

    public function testIsDefinedTerm(): void
    {
        $this->assertInstanceOf( DefinedTerm::class , new ProductType() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA          , ProductType::CONTEXT );
        $this->assertSame( 'https://schema.oihana.xyz' , ProductType::CONTEXT );
    }

    public function testColorComesFromHasColorTrait(): void
    {
        $this->assertContains( HasColor::class , class_uses( ProductType::class ) );
    }

    /**
     * The property name constants live in the products constants trait and are
     * aggregated into Oihana — the class itself exposes no constant of its own.
     */
    public function testPropertyConstantsAggregatedIntoOihana(): void
    {
        $this->assertSame( 'color'     , Oihana::COLOR );
        $this->assertSame( 'stockable' , Oihana::STOCKABLE );
        $this->assertSame( 'trackable' , Oihana::TRACKABLE );
    }

    /**
     * The constructor copies the scalar fields verbatim.
     */
    public function testConstructorCopiesTheScalarProperties(): void
    {
        $type = new ProductType
        ([
            'name'            => 'Bottled wine' ,
            'termCode'        => 'BOTTLE' ,
            Oihana::COLOR     => '#7B1E3A' ,
            Oihana::STOCKABLE => true ,
            Oihana::TRACKABLE => false ,
        ]);

        $this->assertSame ( 'Bottled wine' , $type->name );
        $this->assertSame ( 'BOTTLE'       , $type->termCode );
        $this->assertSame ( '#7B1E3A'      , $type->color );
        $this->assertTrue ( $type->stockable );
        $this->assertFalse( $type->trackable );
    }

    /**
     * The reflection-based hydration path fills the same properties.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheProperties(): void
    {
        $type = ( new Reflection() )->hydrate
        (
            [
                'name'            => 'Bulk wine' ,
                Oihana::COLOR     => '#2E5E4E' ,
                Oihana::STOCKABLE => false ,
                Oihana::TRACKABLE => true ,
            ],
            ProductType::class
        );

        $this->assertInstanceOf( ProductType::class , $type );
        $this->assertSame ( 'Bulk wine' , $type->name );
        $this->assertSame ( '#2E5E4E'   , $type->color );
        $this->assertFalse( $type->stockable );
        $this->assertTrue ( $type->trackable );
    }

    /**
     * Only the initialized properties are serialized — an untouched color stays
     * out of the JSON-LD payload.
     */
    public function testJsonSerializeOmitsTheUninitializedColor(): void
    {
        $type = new ProductType([ 'name' => 'Bottled wine' ]);

        $json = $type->jsonSerialize();

        $this->assertSame( 'Bottled wine' , $json[ 'name' ] );
        $this->assertArrayNotHasKey( Oihana::COLOR , $json );
    }

    public function testJsonSerializeKeepsTheColor(): void
    {
        $type = new ProductType([ 'name' => 'Bottled wine' , Oihana::COLOR => '#7B1E3A' ]);

        $this->assertSame( '#7B1E3A' , $type->jsonSerialize()[ Oihana::COLOR ] );
    }
}
