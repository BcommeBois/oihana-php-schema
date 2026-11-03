<?php

namespace tests\org\schema\enumerations\status ;

use PHPUnit\Framework\TestCase;

use org\schema\enumerations\status\PaymentAutomaticallyApplied;
use org\schema\enumerations\status\PaymentComplete;
use org\schema\enumerations\status\PaymentDeclined;
use org\schema\enumerations\status\PaymentDue;
use org\schema\enumerations\status\PaymentPastDue;
use org\schema\enumerations\status\PaymentStatusType;
use org\schema\enumerations\StatusEnumeration;

class PaymentStatusTypeTest extends TestCase
{
    public function testIsStatusEnumeration(): void
    {
        $this->assertInstanceOf( StatusEnumeration::class , new PaymentStatusType() );
    }

    public function testMembersExtendPaymentStatusType(): void
    {
        $this->assertInstanceOf( PaymentStatusType::class , new PaymentAutomaticallyApplied() );
        $this->assertInstanceOf( PaymentStatusType::class , new PaymentComplete()             );
        $this->assertInstanceOf( PaymentStatusType::class , new PaymentDeclined()             );
        $this->assertInstanceOf( PaymentStatusType::class , new PaymentDue()                  );
        $this->assertInstanceOf( PaymentStatusType::class , new PaymentPastDue()              );
    }

    /**
     * Each member class serializes to the `@type` its constant names : the class
     * and the string are two ways of saying the same status, and a consumer
     * reading one has to recognise what the other wrote.
     */
    public function testEachMemberSerializesToTheTypeItsConstantNames(): void
    {
        $pairs =
        [
            [ new PaymentAutomaticallyApplied() , PaymentStatusType::PAYMENT_AUTOMATICALLY_APPLIED ] ,
            [ new PaymentComplete()             , PaymentStatusType::PAYMENT_COMPLETE              ] ,
            [ new PaymentDeclined()             , PaymentStatusType::PAYMENT_DECLINED              ] ,
            [ new PaymentDue()                  , PaymentStatusType::PAYMENT_DUE                   ] ,
            [ new PaymentPastDue()              , PaymentStatusType::PAYMENT_PAST_DUE              ] ,
        ];

        foreach ( $pairs as [ $member , $constant ] )
        {
            $this->assertSame( $constant , $member->getSchemaType() );
        }
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.org/PaymentAutomaticallyApplied' , PaymentStatusType::PAYMENT_AUTOMATICALLY_APPLIED );
        $this->assertSame( 'https://schema.org/PaymentComplete'             , PaymentStatusType::PAYMENT_COMPLETE              );
        $this->assertSame( 'https://schema.org/PaymentDeclined'             , PaymentStatusType::PAYMENT_DECLINED              );
        $this->assertSame( 'https://schema.org/PaymentDue'                  , PaymentStatusType::PAYMENT_DUE                   );
        $this->assertSame( 'https://schema.org/PaymentPastDue'              , PaymentStatusType::PAYMENT_PAST_DUE              );
    }

    /**
     * The five members schema.org publishes are all declared : a status read from
     * a payload is either one of them or something to reject.
     */
    public function testTheEnumerationHoldsTheFivePublishedMembers(): void
    {
        $values = PaymentStatusType::getConstantValues() ;

        $this->assertContains( PaymentStatusType::PAYMENT_AUTOMATICALLY_APPLIED , $values );
        $this->assertContains( PaymentStatusType::PAYMENT_COMPLETE              , $values );
        $this->assertContains( PaymentStatusType::PAYMENT_DECLINED              , $values );
        $this->assertContains( PaymentStatusType::PAYMENT_DUE                   , $values );
        $this->assertContains( PaymentStatusType::PAYMENT_PAST_DUE              , $values );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( PaymentStatusType::includes( PaymentStatusType::PAYMENT_PAST_DUE ) );
        $this->assertTrue ( PaymentStatusType::includes( 'https://schema.org/PaymentDue' ) );
        $this->assertFalse( PaymentStatusType::includes( 'https://schema.org/PaymentRefunded' ) );
        $this->assertFalse( PaymentStatusType::includes( 'unknown' ) );
    }

    /**
     * A member class name is not a value of the enumeration : a status written
     * with it does not validate, and has to be written with the constant.
     */
    public function testAMemberClassNameIsNotAValue(): void
    {
        $this->assertFalse( PaymentStatusType::includes( PaymentPastDue::class ) );
    }

    /**
     * The `http://` spelling schema.org still serves is a different string, so a
     * payload carrying it does not validate against this enumeration.
     */
    public function testTheLegacyHttpSpellingIsNotAMember(): void
    {
        $this->assertFalse( PaymentStatusType::includes( 'http://schema.org/PaymentPastDue' ) );
    }
}
