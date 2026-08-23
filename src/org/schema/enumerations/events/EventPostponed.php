<?php

namespace org\schema\enumerations\events;

/**
 * The event has been postponed and no new date has been set.
 *
 * The event's previousStartDate should be set.
 *
 * @see https://schema.org/EventPostponed
 */
class EventPostponed extends EventStatusType
{
    /**
     * The URI this member states, kept towards the enumeration's own constant so the
     * two forms cannot drift apart.
     *
     * @var string|null
     * @since 1.5.0
     */
    protected static ?string $TYPE = EventStatusType::POSTPONED ;
}