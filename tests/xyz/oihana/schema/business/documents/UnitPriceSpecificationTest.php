<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;

use org\schema\UnitPriceSpecification as SchemaUnitPriceSpecification;

use xyz\oihana\schema\business\documents\UnitPriceSpecification;
use xyz\oihana\schema\constants\Oihana;

class UnitPriceSpecificationTest extends TestCase
{
    public function testIsTheSchemaUnitPriceSpecification(): void
    {
        $this->assertInstanceOf( SchemaUnitPriceSpecification::class , new UnitPriceSpecification() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , UnitPriceSpecification::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'printed'       , UnitPriceSpecification::PRINTED );
        $this->assertSame( Oihana::PRINTED , UnitPriceSpecification::PRINTED );
    }

    public function testDefaults(): void
    {
        $this->assertNull( new UnitPriceSpecification()->printed ?? null );
    }

    public function testConstructorHydratesPrinted(): void
    {
        $component = new UnitPriceSpecification
        ([
            'price'                         => 40.0  ,
            'priceCurrency'                 => 'EUR' ,
            UnitPriceSpecification::PRINTED => false ,
        ]);

        $this->assertFalse( $component->printed ) ;
        $this->assertSame( 40.0 , $component->price ) ;
    }

    /**
     * The type a component serializes with is its parent's : the subclass
     * exists to carry one flag, not to rename what consumers already read.
     */
    public function testSerializesWithTheSchemaType(): void
    {
        $json = json_decode( json_encode( new UnitPriceSpecification( [ 'price' => 40.0 ] ) ) , true ) ;

        $this->assertSame( 'UnitPriceSpecification' , $json[ '@type' ] ) ;
    }

    /**
     * A printed component — the common case — says nothing : only a component
     * left out of the document carries the flag.
     */
    public function testAPrintedComponentSerializesWithoutTheFlag(): void
    {
        $json = json_decode( json_encode( new UnitPriceSpecification( [ 'price' => 40.0 ] ) ) , true ) ;

        $this->assertArrayNotHasKey( UnitPriceSpecification::PRINTED , $json ) ;
    }

    public function testAnUnprintedComponentSerializesTheFlag(): void
    {
        $json = json_decode
        (
            json_encode( new UnitPriceSpecification( [ 'price' => 40.0 , UnitPriceSpecification::PRINTED => false ] ) ) ,
            true
        ) ;

        $this->assertFalse( $json[ UnitPriceSpecification::PRINTED ] ) ;
    }
}
