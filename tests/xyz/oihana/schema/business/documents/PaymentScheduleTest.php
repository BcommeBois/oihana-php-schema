<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\PaymentInstallment;
use xyz\oihana\schema\business\documents\PaymentSchedule;
use xyz\oihana\schema\constants\Oihana;

class PaymentScheduleTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new PaymentSchedule() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , PaymentSchedule::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'installments' , PaymentSchedule::INSTALLMENTS );
        $this->assertSame( Oihana::INSTALLMENTS , PaymentSchedule::INSTALLMENTS );
    }

    public function testDefaults(): void
    {
        $schedule = new PaymentSchedule() ;

        $this->assertNull( $schedule->installments ?? null );
    }

    public function testConstructorKeepsInstallmentsAsRawArrays(): void
    {
        $schedule = new PaymentSchedule
        ([
            PaymentSchedule::INSTALLMENTS => [ [ 'dueDate' => '2026-01-01' , 'percentage' => 30.0 ] ] ,
        ]);

        $this->assertIsArray( $schedule->installments[ 0 ] ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesInstallmentsIntoObjects(): void
    {
        $schedule = new Reflection()->hydrate
        (
            [
                PaymentSchedule::INSTALLMENTS =>
                [
                    [ 'dueDate' => '2026-01-01' , 'percentage' => 30.0 ] ,
                    [ 'dueDate' => '2026-03-01' , 'percentage' => 70.0 ] ,
                ],
            ],
            PaymentSchedule::class
        );

        $this->assertCount( 2 , $schedule->installments ) ;
        $this->assertContainsOnlyInstancesOf( PaymentInstallment::class , $schedule->installments ) ;
        $this->assertSame( '2026-01-01' , $schedule->installments[ 0 ]->dueDate ) ;
        $this->assertSame( 70.0         , $schedule->installments[ 1 ]->percentage ) ;
    }
}
