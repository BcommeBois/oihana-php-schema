<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

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
     * @throws ReflectionException
     */
    public function testReflectionHydratesRateIntoMonetaryAmount(): void
    {
        $rule = new Reflection()->hydrate
        (
            [ EcoFeeRule::RATE => [ 'value' => 0.25 , 'currency' => 'EUR' ] ],
            EcoFeeRule::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $rule->rate ) ;
        $this->assertSame( 0.25 , $rule->rate->value ) ;
    }
}
