<?php

namespace org\schema\enumerations\events;

/**
 * The event is taking place or has taken place on the startDate as scheduled.
 *
 * Use of this value is optional, as it is assumed by default.
 *
 * @see https://schema.org/EventScheduled
 */
class EventScheduled extends EventStatusType
{
    /**
     * The URI this member states, kept towards the enumeration's own constant so the
     * two forms cannot drift apart.
     *
     * @var string|null
     * @since 1.5.0
     */
    protected static ?string $TYPE = EventStatusType::SCHEDULED ;
}