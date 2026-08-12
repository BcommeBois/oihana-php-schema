<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\QuantitativeValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\products\PhysicalQuantity;

class PhysicalQuantityTest extends TestCase
{
    /**
     * Anything typed on the mirror class accepts it unchanged : that is what
     * makes the specialization free of consequences elsewhere.
     */
    public function testIsQuantitativeValue(): void
    {
        $this->assertInstanceOf( QuantitativeValue::class , new PhysicalQuantity() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , PhysicalQuantity::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'valueReference' , Oihana::VALUE_REFERENCE );
        $this->assertSame( 'volume'         , Oihana::VOLUME          );
        $this->assertSame( 'weight'         , Oihana::WEIGHT          );
    }

    public function testDefaults(): void
    {
        $quantity = new PhysicalQuantity() ;

        $this->assertNull( $quantity->volume ?? null );
        $this->assertNull( $quantity->weight ?? null );
    }

    /**
     * The inherited quantity keeps working : the node still says how much of
     * something there is, and now also what it weighs.
     */
    public function testCarriesItsQuantityBesideItsWeight(): void
    {
        $quantity = new PhysicalQuantity
        ([
            Oihana::VALUE     => 1 ,
            Oihana::UNIT_CODE => 'PF' ,
            Oihana::WEIGHT    => 15.419 ,
        ]);

        $this->assertSame( 1      , $quantity->value    ) ;
        $this->assertSame( 'PF'   , $quantity->unitCode ) ;
        $this->assertSame( 15.419 , $quantity->weight   ) ;
    }

    public function testWeightAndVolumeKeepAPlainNumberAsRead(): void
    {
        $quantity = new PhysicalQuantity
        ([
            Oihana::VOLUME => 0.0312 ,
            Oihana::WEIGHT => 15.419 ,
        ]);

        $this->assertSame( 0.0312 , $quantity->volume ) ;
        $this->assertSame( 15.419 , $quantity->weight ) ;
    }

    /**
     * The constructor assigns raw : a measure read as a row waits in the
     * property instead of raising on it.
     */
    public function testConstructorKeepsStructuredMeasuresAsRawArrays(): void
    {
        $quantity = new PhysicalQuantity
        ([
            Oihana::VOLUME => [ Oihana::VALUE => 0.0312 , Oihana::UNIT_CODE => 'MTQ' ] ,
            Oihana::WEIGHT => [ Oihana::VALUE => 15.419 , Oihana::UNIT_CODE => 'KGM' ] ,
        ]);

        $this->assertIsArray( $quantity->volume ) ;
        $this->assertIsArray( $quantity->weight ) ;
    }

    /**
     * A measure that states its unit comes back typed, so a consumer reads the
     * unit instead of assuming one.
     * @throws ReflectionException
     */
    public function testReflectionHydratesMeasuresThatStateTheirUnit(): void
    {
        $quantity = new Reflection()->hydrate
        (
            [
                Oihana::VOLUME => [ Oihana::VALUE => 0.0312 , Oihana::UNIT_CODE => 'MTQ' ] ,
                Oihana::WEIGHT => [ Oihana::VALUE => 15.419 , Oihana::UNIT_CODE => 'KGM' ] ,
            ],
            PhysicalQuantity::class
        );

        $this->assertInstanceOf( QuantitativeValue::class , $quantity->volume ) ;
        $this->assertSame( 0.0312 , $quantity->volume->value    ) ;
        $this->assertSame( 'MTQ'  , $quantity->volume->unitCode ) ;

        $this->assertInstanceOf( QuantitativeValue::class , $quantity->weight ) ;
        $this->assertSame( 15.419 , $quantity->weight->value    ) ;
        $this->assertSame( 'KGM'  , $quantity->weight->unitCode ) ;
    }

    /**
     * A level points at the next one, and hydration follows the chain all the
     * way down : a weight is read the same way wherever it sits.
     * @throws ReflectionException
     */
    public function testReflectionHydratesTheWholeChain(): void
    {
        $quantity = new Reflection()->hydrate
        (
            [
                Oihana::VALUE  => 1 ,
                Oihana::WEIGHT => 10.99 ,
                Oihana::VALUE_REFERENCE =>
                [
                    Oihana::VALUE  => 1.403 ,
                    Oihana::WEIGHT => 15.419 ,
                    Oihana::VALUE_REFERENCE => [ Oihana::VALUE => 84 , Oihana::WEIGHT => 1295.2 ] ,
                ]
            ],
            PhysicalQuantity::class
        );

        $package = $quantity->valueReference ;
        $pallet  = $package->valueReference ;

        $this->assertInstanceOf( PhysicalQuantity::class , $package ) ;
        $this->assertInstanceOf( PhysicalQuantity::class , $pallet  ) ;

        $this->assertSame( 15.419 , $package->weight ) ;
        $this->assertSame( 1295.2 , $pallet->weight  ) ;
    }
}
