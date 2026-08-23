<?php

namespace org\schema\enumerations\events;

/**
 * The event has been rescheduled.
 *
 * The event's previousStartDate should be set to the old date and the startDate should be set to the event's new date.
 * (If the event has been rescheduled multiple times, the previousStartDate property may be repeated.)
 *
 * @see https://schema.org/EventRescheduled
 */
class EventRescheduled extends EventStatusType
{
    /**
     * The URI this member states, kept towards the enumeration's own constant so the
     * two forms cannot drift apart.
     *
     * @var string|null
     * @since 1.5.0
     */
    protected static ?string $TYPE = EventStatusType::RESCHEDULED ;
}