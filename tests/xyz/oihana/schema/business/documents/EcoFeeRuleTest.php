<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Product;
use org\schema\StructuredValue;
use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\business\documents\EcoFeeRule;
use xyz\oihana\schema\constants\Oihana;

class EcoFeeRuleTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new EcoFeeRule() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , EcoFeeRule::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'category'      , EcoFeeRule::CATEGORY      );
        $this->assertSame( 'rate'          , EcoFeeRule::RATE          );
        $this->assertSame( 'validFrom'     , EcoFeeRule::VALID_FROM    );
        $this->assertSame( 'validThrough'  , EcoFeeRule::VALID_THROUGH );

        $this->assertSame( Oihana::VALID_FROM , EcoFeeRule::VALID_FROM );
    }

    public function testDefaults(): void
    {
        $rule = new EcoFeeRule() ;

        $this->assertNull( $rule->category     ?? null );
        $this->assertNull( $rule->rate         ?? null );
        $this->assertNull( $rule->validFrom    ?? null );
        $this->assertNull( $rule->validThrough ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $rule = new EcoFeeRule
        ([
            EcoFeeRule::CATEGORY      => 'small-electronics' ,
            EcoFeeRule::VALID_FROM    => '2026-01-01' ,
            EcoFeeRule::VALID_THROUGH => '2026-12-31' ,
        ]);

        $this->assertSame( 'small-electronics' , $rule->category     ) ;
        $this->assertSame( '2026-01-01'        , $rule->validFrom    ) ;
        $this->assertSame( '2026-12-31'        , $rule->validThrough ) ;
    }

    /**
     * A rate is charged on a physical measure — a weight, a surface, a count —
     * so the unit is half of it. `MonetaryAmount` had no place to put the unit
     * and could only say « 215 EUR » where the rule says « 215 EUR per tonne ».
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesRateIntoUnitPriceSpecification(): void
    {
        $rule = new Reflection()->hydrate
        (
            [ EcoFeeRule::RATE => [ 'price' => 215 , 'priceCurrency' => 'EUR' , 'unitCode' => 'TNE' ] ],
            EcoFeeRule::class
        );

        $this->assertInstanceOf( UnitPriceSpecification::class , $rule->rate ) ;
        $this->assertSame( 215   , $rule->rate->price         ) ;
        $this->assertSame( 'EUR' , $rule->rate->priceCurrency ) ;
        $this->assertSame( 'TNE' , $rule->rate->unitCode      ) ;
    }

    public function testConstructorKeepsCategoryAsRawArray(): void
    {
        $rule = new EcoFeeRule([ EcoFeeRule::CATEGORY => [ 'codeValue' => 'EEE' ] ]) ;

        $this->assertIsArray( $rule->category ) ;
        $this->assertSame( 'EEE' , $rule->category[ 'codeValue' ] ) ;
    }

    /**
     * The name says category because that is the common case, but a rule may
     * concern exactly one item — a source publishing its rates item by item is
     * the ordinary way of saying so, not an exception to model around.
     *
     * @throws ReflectionException
     */
    public function testTheRuleCanAlsoTargetASingleItem(): void
    {
        $rule = new EcoFeeRule([ EcoFeeRule::CATEGORY => new Product([ 'name' => 'Oak flooring' ]) ]) ;

        $this->assertInstanceOf( Product::class , $rule->category ) ;
        $this->assertSame( 'Oak flooring' , $rule->category->name ) ;
    }
}
