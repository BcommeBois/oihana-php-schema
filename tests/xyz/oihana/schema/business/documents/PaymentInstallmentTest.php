<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;
use org\schema\enumerations\status\PaymentComplete;

use xyz\oihana\schema\business\documents\PaymentInstallment;
use xyz\oihana\schema\business\documents\PaymentReminder;
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
        $this->assertSame( 'amount'        , PaymentInstallment::AMOUNT         );
        $this->assertSame( 'dueDate'       , PaymentInstallment::DUE_DATE       );
        $this->assertSame( 'paymentStatus' , PaymentInstallment::PAYMENT_STATUS );
        $this->assertSame( 'percentage'    , PaymentInstallment::PERCENTAGE     );
        $this->assertSame( 'reminders'     , PaymentInstallment::REMINDERS      );

        $this->assertSame( Oihana::DUE_DATE       , PaymentInstallment::DUE_DATE       );
        $this->assertSame( Oihana::PAYMENT_STATUS , PaymentInstallment::PAYMENT_STATUS );
        $this->assertSame( Oihana::REMINDERS      , PaymentInstallment::REMINDERS      );
    }

    public function testDefaults(): void
    {
        $installment = new PaymentInstallment() ;

        $this->assertNull( $installment->amount        ?? null );
        $this->assertNull( $installment->dueDate       ?? null );
        $this->assertNull( $installment->paymentStatus ?? null );
        $this->assertNull( $installment->percentage    ?? null );
        $this->assertNull( $installment->reminders     ?? null );
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

    public function testPaymentStatusAcceptsPaymentStatusTypeMemberClassName(): void
    {
        $installment = new PaymentInstallment([ PaymentInstallment::PAYMENT_STATUS => PaymentComplete::class ]) ;

        $this->assertSame( PaymentComplete::class , $installment->paymentStatus ) ;
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

    /**
     * The installment-level reminders hydrate into an array of
     * {@see PaymentReminder}.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydratesReminders(): void
    {
        $installment = new Reflection()->hydrate
        (
            [
                PaymentInstallment::REMINDERS =>
                [
                    [ 'date' => '2026-02-10' , 'level' => 'first' ] ,
                    [ 'date' => '2026-02-20' ] ,
                ],
            ],
            PaymentInstallment::class
        );

        $this->assertCount( 2 , $installment->reminders ) ;
        $this->assertContainsOnlyInstancesOf( PaymentReminder::class , $installment->reminders ) ;
        $this->assertSame( '2026-02-20' , $installment->reminders[ 1 ]->date ) ;
    }
}
