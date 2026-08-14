<?php

namespace tests\org\schema\enumerations\events ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use org\schema\enumerations\events\EventAttendanceModeEnumeration;
use org\schema\enumerations\events\MixedEventAttendanceMode;
use org\schema\enumerations\events\OfflineEventAttendanceMode;
use org\schema\enumerations\events\OnlineEventAttendanceMode;

class EventAttendanceModeEnumerationTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new EventAttendanceModeEnumeration() );
    }

    public function testMembersExtendTheEnumeration(): void
    {
        $this->assertInstanceOf( EventAttendanceModeEnumeration::class , new MixedEventAttendanceMode()   );
        $this->assertInstanceOf( EventAttendanceModeEnumeration::class , new OfflineEventAttendanceMode() );
        $this->assertInstanceOf( EventAttendanceModeEnumeration::class , new OnlineEventAttendanceMode()  );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.org/MixedEventAttendanceMode'   , EventAttendanceModeEnumeration::MIXED_EVENT_ATTENDANCE_MODE   );
        $this->assertSame( 'https://schema.org/OfflineEventAttendanceMode' , EventAttendanceModeEnumeration::OFFLINE_EVENT_ATTENDANCE_MODE );
        $this->assertSame( 'https://schema.org/OnlineEventAttendanceMode'  , EventAttendanceModeEnumeration::ONLINE_EVENT_ATTENDANCE_MODE  );
    }

    /**
     * Every member class of the enumeration has a constant naming it, and the
     * constant is the URI schema.org publishes for that member — a value read
     * back from stored data has to match what was written there.
     */
    public function testEveryMemberHasItsConstant(): void
    {
        $values = EventAttendanceModeEnumeration::getConstantValues() ;

        $this->assertContains( 'https://schema.org/MixedEventAttendanceMode'   , $values );
        $this->assertContains( 'https://schema.org/OfflineEventAttendanceMode' , $values );
        $this->assertContains( 'https://schema.org/OnlineEventAttendanceMode'  , $values );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( EventAttendanceModeEnumeration::includes( EventAttendanceModeEnumeration::ONLINE_EVENT_ATTENDANCE_MODE ) );
        $this->assertTrue ( EventAttendanceModeEnumeration::includes( 'https://schema.org/MixedEventAttendanceMode' ) );
        $this->assertFalse( EventAttendanceModeEnumeration::includes( 'https://schema.org/HybridEventAttendanceMode' ) );
        $this->assertFalse( EventAttendanceModeEnumeration::includes( 'unknown' ) );
    }

    /**
     * The legacy `http://` spelling is a different string : a consumer storing it
     * is storing something this enumeration does not know.
     */
    public function testTheHttpSpellingIsNotAMember(): void
    {
        $this->assertFalse( EventAttendanceModeEnumeration::includes( 'http://schema.org/OnlineEventAttendanceMode' ) );
    }
}
