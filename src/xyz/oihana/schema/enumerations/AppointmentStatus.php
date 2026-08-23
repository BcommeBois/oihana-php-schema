<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\enumerations\StatusEnumeration;

use ReflectionException;
use xyz\oihana\schema\constants\Oihana;

/**
 * What became of a meeting itself — as opposed to what became of the slot it was booked in.
 *
 * Distinct from {@see \org\schema\enumerations\events\EventStatusType}, which tracks the
 * *time slot* (scheduled, rescheduled, postponed, cancelled) and, deliberately, publishes
 * no member for « it happened ». A calendar needs to know a meeting moved ; a report needs
 * to know it took place. The two axes answer different questions and are read together :
 * a rescheduled meeting is still to come, a done one no longer moves.
 *
 * | Constant  | Description                                                            | Value                                                    |
 * |-----------|-------------------------------------------------------------------------|-----------------------------------------------------------|
 * | CANCELLED | The meeting was called off before it took place.                        | https://schema.oihana.xyz/AppointmentStatus#Cancelled   |
 * | DONE      | The meeting took place. A report may be written from here on.            | https://schema.oihana.xyz/AppointmentStatus#Done         |
 * | NO_SHOW   | The meeting was kept, and the other party was not there.                | https://schema.oihana.xyz/AppointmentStatus#NoShow       |
 * | PLANNED   | The meeting is booked and has not taken place yet.                      | https://schema.oihana.xyz/AppointmentStatus#Planned      |
 *
 * 🔑 **A status states itself two ways, and both spell the same URI.** The bare constant
 * when there is nothing more to say ; the member class — {@see AppointmentCancelled},
 * {@see AppointmentNoShow}… — when there is, a cancelled meeting carrying the reason it
 * was called off in its `description`. A consumer comparing identifiers must never have
 * to know which form was written, which is why every member states its URI in
 * {@see \org\schema\Thing::$additionalType} the moment it is built.
 *
 * 🔑 **`NO_SHOW` is not `CANCELLED`.** A cancellation is announced and frees the time ;
 * an absence is discovered on the doorstep and costs the journey. Counting them together
 * hides the only one worth acting on.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.5.0
 */
class AppointmentStatus extends StatusEnumeration
{
    /**
     * Creates a new AppointmentStatus instance.
     *
     * A member states its own URI in {@see \org\schema\Thing::$additionalType}, so the
     * object form carries the identifier the bare constant carries. `??=` leaves a caller
     * free to impose something else, and never overwrites a value read back from a store.
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
     * The @context of the json-ld representation of the thing.
     *
     * Declared because this enumeration is now instantiated : its member classes
     * serialize, and a status of this vocabulary must not publish itself under the
     * Schema.org context inherited from {@see \org\schema\Thing}.
     *
     * @since 1.5.0
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The meeting was called off before it took place.
     */
    public const string CANCELLED = 'https://schema.oihana.xyz/AppointmentStatus#Cancelled' ;

    /**
     * The meeting took place. A report may be written from here on.
     */
    public const string DONE = 'https://schema.oihana.xyz/AppointmentStatus#Done' ;

    /**
     * The meeting was kept, and the other party was not there.
     */
    public const string NO_SHOW = 'https://schema.oihana.xyz/AppointmentStatus#NoShow' ;

    /**
     * The meeting is booked and has not taken place yet.
     */
    public const string PLANNED = 'https://schema.oihana.xyz/AppointmentStatus#Planned' ;

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
