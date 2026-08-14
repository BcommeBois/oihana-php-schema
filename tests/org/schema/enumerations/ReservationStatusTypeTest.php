<?php

namespace tests\org\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\enumerations\ReservationStatusType;
use org\schema\enumerations\StatusEnumeration;

class ReservationStatusTypeTest extends TestCase
{
    public function testIsStatusEnumeration(): void
    {
        $this->assertInstanceOf( StatusEnumeration::class , new ReservationStatusType() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.org/ReservationCancelled' , ReservationStatusType::RESERVATION_CANCELLED );
        $this->assertSame( 'https://schema.org/ReservationConfirmed' , ReservationStatusType::RESERVATION_CONFIRMED );
        $this->assertSame( 'https://schema.org/ReservationHold'      , ReservationStatusType::RESERVATION_HOLD      );
        $this->assertSame( 'https://schema.org/ReservationPending'   , ReservationStatusType::RESERVATION_PENDING   );
    }

    /**
     * The four members schema.org publishes are all declared : a status read from
     * a confirmation payload is either one of them or something to reject.
     */
    public function testTheEnumerationHoldsTheFourPublishedMembers(): void
    {
        $values = ReservationStatusType::getConstantValues() ;

        $this->assertContains( ReservationStatusType::RESERVATION_CANCELLED , $values );
        $this->assertContains( ReservationStatusType::RESERVATION_CONFIRMED , $values );
        $this->assertContains( ReservationStatusType::RESERVATION_HOLD      , $values );
        $this->assertContains( ReservationStatusType::RESERVATION_PENDING   , $values );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( ReservationStatusType::includes( ReservationStatusType::RESERVATION_CONFIRMED ) );
        $this->assertTrue ( ReservationStatusType::includes( 'https://schema.org/ReservationCancelled' ) );
        $this->assertFalse( ReservationStatusType::includes( 'https://schema.org/ReservationRefunded' ) );
        $this->assertFalse( ReservationStatusType::includes( 'unknown' ) );
    }

    /**
     * The `http://` spelling schema.org still serves is a different string, so a
     * payload carrying it does not validate against this enumeration.
     */
    public function testTheLegacyHttpSpellingIsNotAMember(): void
    {
        $this->assertFalse( ReservationStatusType::includes( 'http://schema.org/ReservationConfirmed' ) );
    }
}
