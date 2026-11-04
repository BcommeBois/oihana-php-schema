<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\CompoundPriceSpecification as SchemaCompoundPriceSpecification;

use xyz\oihana\schema\business\documents\CompoundPriceSpecification;
use xyz\oihana\schema\business\documents\UnitPriceSpecification;
use xyz\oihana\schema\constants\Oihana;

class CompoundPriceSpecificationTest extends TestCase
{
    public function testIsTheSchemaCompoundPriceSpecification(): void
    {
        $this->assertInstanceOf( SchemaCompoundPriceSpecification::class , new CompoundPriceSpecification() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , CompoundPriceSpecification::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheComponentsIntoTheDocumentClass(): void
    {
        $price = new Reflection()->hydrate
        (
            [
                'priceComponent' =>
                [
                    [ 'price' => 36.0 , 'priceCurrency' => 'EUR' ] ,
                    [ 'price' => 40.0 , 'priceCurrency' => 'EUR' , UnitPriceSpecification::PRINTED => false ] ,
                ] ,
            ] ,
            CompoundPriceSpecification::class
        );

        $this->assertIsArray( $price->priceComponent ) ;
        $this->assertContainsOnlyInstancesOf( UnitPriceSpecification::class , $price->priceComponent ) ;
        $this->assertNull( $price->priceComponent[ 0 ]->printed ?? null ) ;
        $this->assertFalse( $price->priceComponent[ 1 ]->printed ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testSerializesWithTheSchemaTypes(): void
    {
        $price = new Reflection()->hydrate
        (
            [ 'priceComponent' => [ [ 'price' => 40.0 , UnitPriceSpecification::PRINTED => false ] ] ] ,
            CompoundPriceSpecification::class
        );

        $json = json_decode( json_encode( $price ) , true ) ;

        $this->assertSame( 'CompoundPriceSpecification' , $json[ '@type' ] ) ;
        $this->assertSame( 'UnitPriceSpecification'     , $json[ 'priceComponent' ][ 0 ][ '@type' ] ) ;
        $this->assertFalse( $json[ 'priceComponent' ][ 0 ][ UnitPriceSpecification::PRINTED ] ) ;
    }
}
