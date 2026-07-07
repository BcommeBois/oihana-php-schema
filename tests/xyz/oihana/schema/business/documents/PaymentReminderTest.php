<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;
use org\schema\enumerations\status\PotentialActionStatus;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\PaymentReminder;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\PaymentReminderChannel;
use xyz\oihana\schema\enumerations\PaymentReminderLevel;

class PaymentReminderTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new PaymentReminder() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , PaymentReminder::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'adjustments'   , PaymentReminder::ADJUSTMENTS    );
        $this->assertSame( 'amountClaimed' , PaymentReminder::AMOUNT_CLAIMED );
        $this->assertSame( 'channel'       , PaymentReminder::CHANNEL        );
        $this->assertSame( 'date'          , PaymentReminder::DATE           );
        $this->assertSame( 'level'         , PaymentReminder::LEVEL          );
        $this->assertSame( 'note'          , PaymentReminder::NOTE           );
        $this->assertSame( 'status'        , PaymentReminder::STATUS         );

        $this->assertSame( Oihana::AMOUNT_CLAIMED , PaymentReminder::AMOUNT_CLAIMED );
        $this->assertSame( Oihana::CHANNEL        , PaymentReminder::CHANNEL        );
    }

    public function testDefaults(): void
    {
        $reminder = new PaymentReminder() ;

        $this->assertNull( $reminder->adjustments   ?? null );
        $this->assertNull( $reminder->amountClaimed ?? null );
        $this->assertNull( $reminder->channel       ?? null );
        $this->assertNull( $reminder->date          ?? null );
        $this->assertNull( $reminder->level         ?? null );
        $this->assertNull( $reminder->note          ?? null );
        $this->assertNull( $reminder->status        ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $reminder = new PaymentReminder
        ([
            PaymentReminder::DATE    => '2026-03-15' ,
            PaymentReminder::LEVEL   => PaymentReminderLevel::SECOND_REMINDER ,
            PaymentReminder::CHANNEL => PaymentReminderChannel::EMAIL ,
            PaymentReminder::STATUS  => PotentialActionStatus::class ,
            PaymentReminder::NOTE    => 'ref INT-2026-002' ,
        ]);

        $this->assertSame( '2026-03-15' , $reminder->date ) ;
        $this->assertSame( PaymentReminderLevel::SECOND_REMINDER , $reminder->level ) ;
        $this->assertSame( PaymentReminderChannel::EMAIL , $reminder->channel ) ;
        $this->assertSame( PotentialActionStatus::class , $reminder->status ) ;
        $this->assertSame( 'ref INT-2026-002' , $reminder->note ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesNestedValueObjects(): void
    {
        $reminder = new Reflection()->hydrate
        (
            [
                PaymentReminder::AMOUNT_CLAIMED => [ 'value' => 120 , 'currency' => 'EUR' ] ,
                PaymentReminder::ADJUSTMENTS    =>
                [
                    [ 'type' => 'surcharge' , 'reason' => 'Late fee' , 'amount' => [ 'value' => 40 , 'currency' => 'EUR' ] ] ,
                ] ,
            ],
            PaymentReminder::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $reminder->amountClaimed ) ;
        $this->assertSame( 120 , $reminder->amountClaimed->value ) ;

        $this->assertInstanceOf( Adjustment::class , $reminder->adjustments[ 0 ] ) ;
        $this->assertSame( 'Late fee' , $reminder->adjustments[ 0 ]->reason ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $reminder->adjustments[ 0 ]->amount ) ;
        $this->assertSame( 40 , $reminder->adjustments[ 0 ]->amount->value ) ;
    }
}
