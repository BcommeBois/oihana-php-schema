<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\AppliedEcoFee;
use xyz\oihana\schema\business\documents\EcoFeeRule;
use xyz\oihana\schema\constants\Oihana;

class AppliedEcoFeeTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new AppliedEcoFee() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , AppliedEcoFee::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'amount'   , AppliedEcoFee::AMOUNT   );
        $this->assertSame( 'quantity' , AppliedEcoFee::QUANTITY );
        $this->assertSame( 'rule'     , AppliedEcoFee::RULE     );

        $this->assertSame( Oihana::QUANTITY , AppliedEcoFee::QUANTITY );
    }

    public function testDefaults(): void
    {
        $applied = new AppliedEcoFee() ;

        $this->assertNull( $applied->amount   ?? null );
        $this->assertNull( $applied->quantity ?? null );
        $this->assertNull( $applied->rule     ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $applied = new AppliedEcoFee
        ([
            AppliedEcoFee::QUANTITY => 3 ,
            AppliedEcoFee::RULE     => 'eco-fee-rules/small-electronics' ,
        ]);

        $this->assertSame( 3 , $applied->quantity ) ;
        $this->assertSame( 'eco-fee-rules/small-electronics' , $applied->rule ) ;
    }

    public function testRuleAcceptsAResolvedObject(): void
    {
        $rule    = new EcoFeeRule([ 'category' => 'small-electronics' ]) ;
        $applied = new AppliedEcoFee([ AppliedEcoFee::RULE => $rule ]) ;

        $this->assertSame( $rule , $applied->rule ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesAmountIntoMonetaryAmount(): void
    {
        $applied = new Reflection()->hydrate
        (
            [ AppliedEcoFee::AMOUNT => [ 'value' => 0.75 , 'currency' => 'EUR' ] ],
            AppliedEcoFee::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $applied->amount ) ;
        $this->assertSame( 0.75 , $applied->amount->value ) ;
    }
}
