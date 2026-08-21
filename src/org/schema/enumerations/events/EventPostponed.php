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

}