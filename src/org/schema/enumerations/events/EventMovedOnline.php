<?php

namespace org\schema\enumerations\events;

/**
 * Indicates that the event was changed to allow online participation.
 *
 * See eventAttendanceMode for specifics of whether it is now fully or partially online.
 *
 * @see https://schema.org/EventMovedOnline
 */
class EventMovedOnline extends EventStatusType
{
    /**
     * The URI this member states, kept towards the enumeration's own constant so the
     * two forms cannot drift apart.
     *
     * @var string|null
     * @since 1.5.0
     */
    protected static ?string $TYPE = EventStatusType::MOVED_ONLINE ;
}