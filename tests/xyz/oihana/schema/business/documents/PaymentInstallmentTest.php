<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\PaymentInstallment;
use xyz\oihana\schema\constants\Oihana;

class PaymentInstallmentTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new PaymentInstallment() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , PaymentInstallment::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'amount'     , PaymentInstallment::AMOUNT     );
        $this->assertSame( 'dueDate'    , PaymentInstallment::DUE_DATE   );
        $this->assertSame( 'percentage' , PaymentInstallment::PERCENTAGE );

        $this->assertSame( Oihana::DUE_DATE , PaymentInstallment::DUE_DATE );
    }

    public function testDefaults(): void
    {
        $installment = new PaymentInstallment() ;

        $this->assertNull( $installment->amount     ?? null );
        $this->assertNull( $installment->dueDate    ?? null );
        $this->assertNull( $installment->percentage ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $installment = new PaymentInstallment
        ([
            PaymentInstallment::DUE_DATE   => '2026-01-01' ,
            PaymentInstallment::PERCENTAGE => 30.0 ,
        ]);

        $this->assertSame( '2026-01-01' , $installment->dueDate ) ;
        $this->assertSame( 30.0         , $installment->percentage ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesAmountIntoMonetaryAmount(): void
    {
        $installment = new Reflection()->hydrate
        (
            [ PaymentInstallment::AMOUNT => [ 'value' => 300 , 'currency' => 'EUR' ] ],
            PaymentInstallment::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $installment->amount ) ;
        $this->assertSame( 300 , $installment->amount->value ) ;
    }
}
