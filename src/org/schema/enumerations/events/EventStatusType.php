<?php

namespace org\schema\enumerations\events;

use org\schema\Enumeration;

/**
 * An enumeration type whose instances represent several states that an Event may be in.
 * @see https://schema.org/EventStatusType
 */
class EventStatusType extends Enumeration
{
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
}