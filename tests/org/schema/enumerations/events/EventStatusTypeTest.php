<?php

namespace tests\org\schema\enumerations\events ;

use PHPUnit\Framework\TestCase;

use org\schema\constants\Schema;
use org\schema\Enumeration;
use org\schema\enumerations\events\EventCancelled;
use org\schema\enumerations\events\EventMovedOnline;
use org\schema\enumerations\events\EventPostponed;
use org\schema\enumerations\events\EventRescheduled;
use org\schema\enumerations\events\EventScheduled;
use org\schema\enumerations\events\EventStatusType;

class EventStatusTypeTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new EventStatusType() );
    }

    public function testMembersExtendTheEnumeration(): void
    {
        $this->assertInstanceOf( EventStatusType::class , new EventCancelled()    );
        $this->assertInstanceOf( EventStatusType::class , new EventMovedOnline()  );
        $this->assertInstanceOf( EventStatusType::class , new EventPostponed()    );
        $this->assertInstanceOf( EventStatusType::class , new EventRescheduled()  );
        $this->assertInstanceOf( EventStatusType::class , new EventScheduled()    );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'https://schema.org/EventCancelled'   , EventStatusType::CANCELLED    );
        $this->assertSame( 'https://schema.org/EventMovedOnline' , EventStatusType::MOVED_ONLINE );
        $this->assertSame( 'https://schema.org/EventPostponed'   , EventStatusType::POSTPONED    );
        $this->assertSame( 'https://schema.org/EventRescheduled' , EventStatusType::RESCHEDULED  );
        $this->assertSame( 'https://schema.org/EventScheduled'   , EventStatusType::SCHEDULED    );
    }

    /**
     * The two ways of stating a status answer the same string.
     *
     * A status may be written as the bare constant, or as the member class when it
     * has something more to say — a reason, a name. The choice is only free as long
     * as both spell the same URI : a consumer comparing strings must not have to
     * know which one was used. Renaming a member class would break that silently,
     * which is why it is asserted rather than assumed.
     */
    public function testEachMemberClassAnswersItsConstant(): void
    {
        $this->assertSame( EventStatusType::CANCELLED    , EventCancelled::getSchemaType()   );
        $this->assertSame( EventStatusType::MOVED_ONLINE , EventMovedOnline::getSchemaType() );
        $this->assertSame( EventStatusType::POSTPONED    , EventPostponed::getSchemaType()   );
        $this->assertSame( EventStatusType::RESCHEDULED  , EventRescheduled::getSchemaType() );
        $this->assertSame( EventStatusType::SCHEDULED    , EventScheduled::getSchemaType()   );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( EventStatusType::includes( EventStatusType::CANCELLED ) );
        $this->assertTrue ( EventStatusType::includes( 'https://schema.org/EventPostponed' ) );
        $this->assertFalse( EventStatusType::includes( 'https://schema.org/EventHappened' ) );
        $this->assertFalse( EventStatusType::includes( 'unknown' ) );
    }

    /**
     * The legacy `http://` spelling is a different string : a consumer storing it
     * is storing something this enumeration does not know.
     */
    public function testTheHttpSpellingIsNotAMember(): void
    {
        $this->assertFalse( EventStatusType::includes( 'http://schema.org/EventCancelled' ) );
    }

    /**
     * A member class is a `Thing`, so it carries a reason where the bare constant
     * carries none — which is the whole point of offering both.
     */
    public function testAMemberCarriesItsReason(): void
    {
        $cancelled = new EventCancelled([ Schema::DESCRIPTION => 'Called off the day before.' ] );

        $this->assertSame( 'Called off the day before.' , $cancelled->description );

        $json = $cancelled->jsonSerialize() ;

        $this->assertSame( 'EventCancelled'             , $json[ Schema::AT_TYPE ]     ?? null );
        $this->assertSame( 'Called off the day before.' , $json[ Schema::DESCRIPTION ] ?? null );
    }
}
