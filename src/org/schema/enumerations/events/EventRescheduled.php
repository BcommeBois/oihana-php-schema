<?php

namespace org\schema\enumerations\events;

use org\schema\Enumeration;

/**
 * The event has been rescheduled. The event's previousStartDate should be set to the old date and the startDate should be set to the event's new date. (If the event has been rescheduled multiple times, the previousStartDate property may be repeated.)
 * @see https://schema.org/EventRescheduled
 */
class EventRescheduled extends Enumeration
{

}