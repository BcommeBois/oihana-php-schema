<?php

namespace tests\org\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\constants\Schema;
use org\schema\enumerations\events\EventCancelled;
use org\schema\enumerations\events\EventPostponed;
use org\schema\enumerations\events\EventStatusType;

use function org\schema\helpers\hydrate\hydrateEventStatus;

final class HydrateEventStatusTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesAStatusStatedAsAnObject(): void
    {
        $status = hydrateEventStatus
        ([
            Schema::AT_TYPE     => 'EventCancelled' ,
            Schema::DESCRIPTION => 'Called off the day before.' ,
        ]) ;

        $this->assertInstanceOf( EventCancelled::class , $status ) ;
        $this->assertSame( 'Called off the day before.' , $status->description ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAStatusFromItsStatedUri(): void
    {
        $status = hydrateEventStatus( [ Schema::ADDITIONAL_TYPE => EventStatusType::POSTPONED ] ) ;

        $this->assertInstanceOf( EventPostponed::class , $status ) ;
    }

    /**
     * The bare constant is the other half of the contract : a consumer hands over
     * whichever form was stored, without knowing which one it was.
     *
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateEventStatus() ) ;
        $this->assertSame( EventStatusType::CANCELLED , hydrateEventStatus( EventStatusType::CANCELLED ) ) ;
        $this->assertSame( 'raw' , hydrateEventStatus( 'raw' ) ) ;

        $cancelled = new EventCancelled() ;
        $this->assertSame( $cancelled , hydrateEventStatus( $cancelled ) ) ;
    }

    /**
     * A status this vocabulary does not publish keeps what it carried : answering
     * `null` would drop the reason, which is the only thing the object form was
     * written for.
     *
     * @throws ReflectionException
     */
    public function testAnUnknownStatusKeepsItsReason(): void
    {
        $status = hydrateEventStatus
        ([
            Schema::AT_TYPE     => 'EventHappened' ,
            Schema::DESCRIPTION => 'It went ahead.' ,
        ]) ;

        $this->assertInstanceOf( EventStatusType::class , $status ) ;
        $this->assertSame( 'It went ahead.' , $status->description ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $statuses = hydrateEventStatus
        ([
            [ Schema::AT_TYPE => 'EventCancelled' ] ,
            [ Schema::AT_TYPE => 'EventPostponed' ] ,
        ]) ;

        $this->assertIsArray( $statuses ) ;
        $this->assertCount( 2 , $statuses ) ;
        $this->assertContainsOnlyInstancesOf( EventStatusType::class , $statuses ) ;

        $this->assertNull( hydrateEventStatus( [] ) ) ;
    }

    /**
     * The promise the pair holds : the bare constant and the member class answer the
     * **same identifier**, before and after a round trip through `json_encode()` and
     * hydration. A consumer filing meetings in a store must be able to filter
     * « the cancelled ones » without choosing which of the two forms to abandon.
     *
     * @throws ReflectionException
     */
    public function testBothFormsAnswerTheSameIdentifierAcrossARoundTrip(): void
    {
        $bare   = EventStatusType::CANCELLED ;
        $stated = new EventCancelled( [ Schema::DESCRIPTION => 'Called off the day before.' ] ) ;

        $this->assertSame( $bare , $stated->additionalType ) ;

        $decoded = json_decode( json_encode( $stated ) , true ) ;
        $read    = hydrateEventStatus( $decoded ) ;

        $this->assertInstanceOf( EventCancelled::class , $read ) ;
        $this->assertSame( $bare , $read->additionalType ) ;
        $this->assertSame( 'Called off the day before.' , $read->description ) ;

        // And the bare form crosses the same round trip untouched.
        $this->assertSame( $bare , hydrateEventStatus( json_decode( json_encode( $bare ) , true ) ) ) ;
    }

    /**
     * A value already stated by the payload is never overwritten : the store is the
     * authority on what was written, not the class it is read back into.
     *
     * @throws ReflectionException
     */
    public function testAStatedTypeIsNotOverwritten(): void
    {
        $status = hydrateEventStatus
        ([
            Schema::AT_TYPE         => 'EventCancelled' ,
            Schema::ADDITIONAL_TYPE => EventStatusType::CANCELLED ,
        ]) ;

        $this->assertInstanceOf( EventCancelled::class , $status ) ;
        $this->assertSame( EventStatusType::CANCELLED , $status->additionalType ) ;
    }
}
