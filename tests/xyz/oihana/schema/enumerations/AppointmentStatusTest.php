<?php

namespace tests\xyz\oihana\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\constants\Schema;
use org\schema\enumerations\StatusEnumeration;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\AppointmentCancelled;
use xyz\oihana\schema\enumerations\AppointmentDone;
use xyz\oihana\schema\enumerations\AppointmentNoShow;
use xyz\oihana\schema\enumerations\AppointmentPlanned;
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

    public function testMembersExtendTheEnumeration(): void
    {
        $this->assertInstanceOf( AppointmentStatus::class , new AppointmentCancelled() );
        $this->assertInstanceOf( AppointmentStatus::class , new AppointmentDone()      );
        $this->assertInstanceOf( AppointmentStatus::class , new AppointmentNoShow()    );
        $this->assertInstanceOf( AppointmentStatus::class , new AppointmentPlanned()   );
    }

    /**
     * The two ways of stating a status answer the same string.
     *
     * A status may be written as the bare constant, or as the member class when it has
     * something more to say — a reason, a name. The choice is only free as long as both
     * spell the same URI : a consumer comparing strings must not have to know which one
     * was used.
     *
     * 🔑 **The member states its URI, it does not derive it.** The class is named
     * `AppointmentNoShow` where the constant reads `…/AppointmentStatus#NoShow` : no rule
     * takes one to the other, which is exactly why the URI is declared beside the class
     * rather than computed from its name.
     */
    public function testEachMemberStatesItsConstant(): void
    {
        $this->assertSame( AppointmentStatus::CANCELLED , new AppointmentCancelled()->additionalType );
        $this->assertSame( AppointmentStatus::DONE      , new AppointmentDone()->additionalType      );
        $this->assertSame( AppointmentStatus::NO_SHOW   , new AppointmentNoShow()->additionalType    );
        $this->assertSame( AppointmentStatus::PLANNED   , new AppointmentPlanned()->additionalType   );
    }

    /**
     * The head of an enumeration is not a member of itself : it states no URI, and
     * serializes without one.
     */
    public function testTheHeadStatesNoUri(): void
    {
        $status = new AppointmentStatus() ;

        $this->assertFalse( isset( $status->additionalType ) );
        $this->assertArrayNotHasKey( Schema::ADDITIONAL_TYPE , $status->jsonSerialize() );
    }

    /**
     * A member is a `Thing`, so it carries a reason where the bare constant carries
     * none — which is the whole point of offering both. And it publishes itself under
     * this vocabulary's context, not the Schema.org one it would otherwise inherit.
     */
    public function testAMemberCarriesItsReason(): void
    {
        $cancelled = new AppointmentCancelled( [ Schema::DESCRIPTION => 'The customer postponed.' ] );

        $json = $cancelled->jsonSerialize() ;

        $this->assertSame( 'AppointmentCancelled'         , $json[ Schema::AT_TYPE ]         ?? null );
        $this->assertSame( Oihana::SCHEMA                 , $json[ Schema::AT_CONTEXT ]      ?? null );
        $this->assertSame( AppointmentStatus::CANCELLED   , $json[ Schema::ADDITIONAL_TYPE ] ?? null );
        $this->assertSame( 'The customer postponed.'      , $json[ Schema::DESCRIPTION ]     ?? null );
    }

    /**
     * `??=` : a caller may impose something else, and a value read back from a store is
     * never overwritten by the class it is read into.
     */
    public function testAStatedTypeIsNotOverwritten(): void
    {
        $status = new AppointmentDone( [ Schema::ADDITIONAL_TYPE => 'https://example.org/Done' ] );

        $this->assertSame( 'https://example.org/Done' , $status->additionalType );
    }

    /**
     * 🚨 The `TYPE` slot is a static property rather than a class constant : `Enumeration`
     * uses `ConstantsTrait`, which enumerates every constant of the class — a `TYPE`
     * constant holding `null` on the head would join `enums()` and make `includes( null )`
     * answer true, in the very enumeration whose point is to say what a valid status is.
     */
    public function testTheMemberSlotIsNotAMemberOfTheEnumeration(): void
    {
        $this->assertFalse( AppointmentStatus::includes( null ) );
        $this->assertNotContains( null , AppointmentStatus::enums() );
    }
}
