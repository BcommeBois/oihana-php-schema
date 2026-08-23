<?php

namespace org\schema\enumerations\events;

/**
 * The event has been cancelled. If the event has multiple startDate values, all are assumed to be cancelled.
 *
 * Either startDate or previousStartDate may be used to specify the event's cancelled date(s).
 *
 * @see https://schema.org/EventCancelled
 */
class EventCancelled extends EventStatusType
{
    /**
     * The URI this member states, kept towards the enumeration's own constant so the
     * two forms cannot drift apart.
     *
     * @var string|null
     * @since 1.5.0
     */
    protected static ?string $TYPE = EventStatusType::CANCELLED ;
}