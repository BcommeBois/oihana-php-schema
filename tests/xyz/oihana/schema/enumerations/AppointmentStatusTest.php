<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\enumerations\StatusEnumeration;
use xyz\oihana\schema\enumerations\AppointmentStatus;

class AppointmentStatusTest extends TestCase
{
    public function testIsStatusEnumeration(): void
    {
        $this->assertInstanceOf( StatusEnumeration::class , new AppointmentStatus() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/AppointmentStatus#Cancelled' , AppointmentStatus::CANCELLED );
        $this->assertSame( 'https://schema.oihana.xyz/AppointmentStatus#Done'      , AppointmentStatus::DONE      );
        $this->assertSame( 'https://schema.oihana.xyz/AppointmentStatus#NoShow'    , AppointmentStatus::NO_SHOW   );
        $this->assertSame( 'https://schema.oihana.xyz/AppointmentStatus#Planned'   , AppointmentStatus::PLANNED   );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( AppointmentStatus::includes( AppointmentStatus::DONE ) );
        $this->assertFalse( AppointmentStatus::includes( 'unknown' ) );
    }

    /**
     * The two axes are separate vocabularies : what became of the slot, and what
     * became of the meeting. A member of one is never a member of the other, and a
     * reader who mixes them gets no error to warn them.
     */
    public function testItIsNotTheEventStatusVocabulary(): void
    {
        $this->assertFalse( AppointmentStatus::includes( 'https://schema.org/EventCancelled' ) );
        $this->assertFalse( AppointmentStatus::includes( 'https://schema.org/EventScheduled' ) );

        $members =
        [
            AppointmentStatus::CANCELLED , AppointmentStatus::DONE ,
            AppointmentStatus::NO_SHOW   , AppointmentStatus::PLANNED ,
        ];

        foreach ( $members as $value )
        {
            $this->assertStringStartsWith( 'https://schema.oihana.xyz/AppointmentStatus#' , $value );
        }
    }
}
