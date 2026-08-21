<?php

namespace tests\org\schema ;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;
use org\schema\constants\traits\Event as EventProperties;
use org\schema\Event;
use org\schema\enumerations\events\EventAttendanceModeEnumeration;
use org\schema\enumerations\events\EventCancelled;
use org\schema\enumerations\events\EventStatusType;
use org\schema\Offer;
use org\schema\Organization;
use org\schema\Place;
use org\schema\Thing;

class EventTest extends TestCase
{
    /**
     * The one constant of the trait that names no property : nothing in the
     * library declares a `numEvents`, and schema.org does not publish it either.
     */
    private const array ORPHAN_CONSTANTS = [ 'numEvents' ] ;

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new Event() );
    }

    public function testPropertiesDefaultToNull(): void
    {
        $event = new Event();

        $this->assertNull( $event->funding               ?? null );
        $this->assertNull( $event->hasParticipationOffer ?? null );
        $this->assertNull( $event->hasSponsorshipOffer   ?? null );
        $this->assertNull( $event->performer             ?? null );
        $this->assertNull( $event->previousStartDate     ?? null );
        $this->assertNull( $event->recordedIn            ?? null );
        $this->assertNull( $event->sponsor               ?? null );
        $this->assertNull( $event->superEvent            ?? null );
        $this->assertNull( $event->translator            ?? null );
        $this->assertNull( $event->typicalAgeRange       ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorCopiesTheProperties(): void
    {
        $event = new Event
        ([
            Schema::NAME                => 'Oihana Conference 2026' ,
            Schema::START_DATE          => '2026-09-15T09:00:00+02:00' ,
            Schema::PREVIOUS_START_DATE => '2026-09-08T09:00:00+02:00' ,
            Schema::TYPICAL_AGE_RANGE   => '18-' ,
            Schema::LOCATION            => new Place([ Schema::NAME => 'Nantes' ] ) ,
        ]);

        $this->assertSame( 'Oihana Conference 2026'     , $event->name              );
        $this->assertSame( '2026-09-15T09:00:00+02:00'  , $event->startDate         );
        $this->assertSame( '2026-09-08T09:00:00+02:00'  , $event->previousStartDate );
        $this->assertSame( '18-'                        , $event->typicalAgeRange   );
        $this->assertInstanceOf( Place::class           , $event->location          );
    }

    /**
     * A rescheduled event states both dates at once : the one it moved to, and the
     * one it was announced on. Losing the second is losing the whole point of the
     * `eventStatus` beside it.
     */
    public function testPreviousStartDateSitsBesideTheStartDate(): void
    {
        $event = new Event
        ([
            Schema::EVENT_STATUS        => 'https://schema.org/EventRescheduled' ,
            Schema::START_DATE          => '2026-10-01' ,
            Schema::PREVIOUS_START_DATE => '2026-09-15' ,
        ]);

        $this->assertSame( '2026-10-01' , $event->startDate         );
        $this->assertSame( '2026-09-15' , $event->previousStartDate );
    }

    /**
     * `subEvent` and `superEvent` are the two directions of the same link : a
     * conference talk points up, the conference points down, and neither stands in
     * for the other.
     */
    public function testSubEventAndSuperEventAreIndependent(): void
    {
        $conference = new Event([ Schema::NAME => 'Conference' ] );
        $talk       = new Event([ Schema::NAME => 'Talk' , Schema::SUPER_EVENT => $conference ] );

        $conference->subEvent = $talk ;

        $this->assertSame( $conference , $talk->superEvent       );
        $this->assertSame( $talk       , $conference->subEvent   );
        $this->assertNull( $conference->superEvent ?? null       );
    }

    public function testAttendanceModeAcceptsTheEnumerationValues(): void
    {
        $event = new Event([ Schema::EVENT_ATTENDANCE_MODE => EventAttendanceModeEnumeration::MIXED_EVENT_ATTENDANCE_MODE ] );

        $this->assertSame( 'https://schema.org/MixedEventAttendanceMode' , $event->eventAttendanceMode );
        $this->assertTrue( EventAttendanceModeEnumeration::includes( $event->eventAttendanceMode ) );
    }

    /**
     * The two offers an event opens are separate calls with separate audiences —
     * one to take part, one to fund. They are typed the same and must not collapse
     * into a single property.
     */
    public function testParticipationAndSponsorshipOffersAreDistinct(): void
    {
        $event = new Event
        ([
            Schema::HAS_PARTICIPATION_OFFER => new Offer([ Schema::NAME => 'Call for speakers' ] ) ,
            Schema::HAS_SPONSORSHIP_OFFER   => new Offer([ Schema::NAME => 'Sponsor packages'  ] ) ,
        ]);

        $this->assertSame( 'Call for speakers' , $event->hasParticipationOffer->name );
        $this->assertSame( 'Sponsor packages'  , $event->hasSponsorshipOffer->name   );
    }

    /**
     * The people-carrying properties accept a single entity as well as a list :
     * an event with one performer states it without wrapping it in an array.
     */
    public function testPerformerAcceptsOneEntityOrAList(): void
    {
        $single = new Event([ Schema::PERFORMER => new Organization([ Schema::NAME => 'Etchea' ] ) ] );
        $many   = new Event([ Schema::PERFORMER => [ [ Schema::NAME => 'Ada'   ] , [ Schema::NAME => 'Alan' ] ] ] );

        $this->assertInstanceOf( Organization::class , $single->performer );
        $this->assertIsArray( $many->performer );
        $this->assertCount( 2 , $many->performer );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydrationKeepsTheScalarProperties(): void
    {
        $event = ( new Reflection() )->hydrate
        (
            [
                Schema::NAME                        => 'Festival' ,
                Schema::REMAINING_ATTENDEE_CAPACITY => 12 ,
                Schema::IS_ACCESSIBLE_FOR_FREE      => true ,
                Schema::TYPICAL_AGE_RANGE           => '7-9' ,
            ] ,
            Event::class
        );

        $this->assertSame( 'Festival' , $event->name                      );
        $this->assertSame( 12         , $event->remainingAttendeeCapacity );
        $this->assertTrue( $event->isAccessibleForFree                    );
        $this->assertSame( '7-9'      , $event->typicalAgeRange           );
    }

    public function testPropertyNameConstants(): void
    {
        $this->assertSame( 'funder'                , Schema::FUNDER                  );
        $this->assertSame( 'funding'               , Schema::FUNDING                 );
        $this->assertSame( 'hasParticipationOffer' , Schema::HAS_PARTICIPATION_OFFER );
        $this->assertSame( 'hasSponsorshipOffer'   , Schema::HAS_SPONSORSHIP_OFFER   );
        $this->assertSame( 'performer'             , Schema::PERFORMER               );
        $this->assertSame( 'previousStartDate'     , Schema::PREVIOUS_START_DATE     );
        $this->assertSame( 'recordedIn'            , Schema::RECORDED_IN             );
        $this->assertSame( 'sponsor'               , Schema::SPONSOR                 );
        $this->assertSame( 'superEvent'            , Schema::SUPER_EVENT             );
        $this->assertSame( 'translator'            , Schema::TRANSLATOR              );
        $this->assertSame( 'typicalAgeRange'       , Schema::TYPICAL_AGE_RANGE       );
    }

    /**
     * Every constant of the trait names a property that really exists on the class.
     *
     * A constant is only useful as a key nobody has to spell by hand, so one whose
     * value drifts from the property it names — `funded` for `funder` — writes a
     * key the class silently drops. The check reads the trait rather than a hand
     * written list, so a constant added later is covered the day it lands.
     */
    public function testEveryConstantNamesAnExistingProperty(): void
    {
        $constants = ( new ReflectionClass( EventProperties::class ) )->getConstants() ;

        foreach ( $constants as $constant => $property )
        {
            if ( in_array( $property , self::ORPHAN_CONSTANTS , true ) )
            {
                continue ;
            }

            $this->assertTrue
            (
                property_exists( Event::class , $property ) ,
                sprintf( 'Event has no "%s" property, named by the %s constant.' , $property , $constant )
            );
        }
    }

    /**
     * A status states itself two ways, and both have to survive a round trip.
     *
     * The bare constant is the ordinary case. The member class is for when the
     * status has something more to say — why an event was called off — and a stored
     * one comes back as an array before anything types it, which is why `array` is
     * part of the union. Without it the object form could be written in PHP and
     * never read back.
     *
     * @throws ReflectionException
     */
    public function testEventStatusAcceptsTheBareConstantAndTheStatedReason(): void
    {
        $bare = new Event([ Schema::EVENT_STATUS => EventStatusType::CANCELLED ] );

        $this->assertSame( 'https://schema.org/EventCancelled' , $bare->eventStatus );

        $stated = new Event([ Schema::EVENT_STATUS => new EventCancelled([ Schema::DESCRIPTION => 'Called off.' ] ) ] );

        $this->assertInstanceOf( EventCancelled::class , $stated->eventStatus );
        $this->assertSame( 'Called off.' , $stated->eventStatus->description );

        $stored = new Event([ Schema::EVENT_STATUS => [ Schema::AT_TYPE => 'EventCancelled' , Schema::DESCRIPTION => 'Called off.' ] ] );

        $this->assertIsArray( $stored->eventStatus );
    }

}
