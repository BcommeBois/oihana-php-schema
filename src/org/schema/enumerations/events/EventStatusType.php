<?php

namespace org\schema\enumerations\events;

use org\schema\Enumeration;
use org\schema\Thing;
use ReflectionException;

/**
 * An enumeration type whose instances represent several states that an Event may be in.
 *
 * 🔑 **A status states itself two ways, and both spell the same URI.** The bare
 * constant when there is nothing more to say ; the member class — {@see EventCancelled},
 * {@see EventPostponed}… — when there is, a cancelled event carrying the reason it was
 * called off in its `description`. A consumer comparing identifiers must never have to
 * know which form was written, which is why every member states its URI in
 * {@see Thing::$additionalType} the moment it is built.
 *
 * @see https://schema.org/EventStatusType
 */
class EventStatusType extends Enumeration
{
    /**
     * Creates a new EventStatusType instance.
     *
     * A member states its own URI in {@see Thing::$additionalType}, so the object form
     * carries the identifier the bare constant carries. `??=` leaves a caller free to
     * impose something else, and never overwrites a value read back from a store.
     *
     * @param array|object|null $init The optional properties to initialize the status with.
     *
     * @throws ReflectionException
     *
     * @since 1.5.0
     */
    public function __construct( array|object|null $init = null )
    {
        parent::__construct( $init ) ;

        if( static::$TYPE !== null )
        {
            $this->additionalType ??= static::$TYPE ;
        }
    }

    /**
     * The event has been cancelled. If the event has multiple startDate values, all are assumed to be cancelled.
     */
    public const string CANCELLED = 'https://schema.org/EventCancelled' ;

    /**
     * Indicates that the event was changed to allow online participation.
     */
    public const string MOVED_ONLINE = 'https://schema.org/EventMovedOnline' ;

    /**
     * The event has been postponed and no new date has been set.
     */
    public const string POSTPONED = 'https://schema.org/EventPostponed' ;

    /**
     * The event has been rescheduled.
     */
    public const string RESCHEDULED = 'https://schema.org/EventRescheduled' ;

    /**
     * The event has been scheduled.
     */
    public const string SCHEDULED = 'https://schema.org/EventScheduled' ;

    /**
     * The URI the member class states — `null` on the enumeration head, which is not a
     * member of itself.
     *
     * Each member redeclares it towards the constant above it rather than towards a
     * literal, so the two forms cannot drift apart : renaming one is a compile-time
     * matter, not a silent change of identifier.
     *
     * ⚠️ **A static property rather than a class constant**, on purpose : `Enumeration`
     * uses `ConstantsTrait`, which enumerates *every* constant of the class — a `TYPE`
     * constant holding `null` would join `enums()` and make `includes( null )` answer
     * true, in the very enumeration whose point is to say what a valid status is.
     *
     * @var string|null
     * @since 1.5.0
     */
    protected static ?string $TYPE = null ;


}
