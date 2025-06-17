<?php

namespace org\schema\events\enumerations;

use org\schema\Enumeration;

/**
 * The event has been cancelled. If the event has multiple startDate values, all are assumed to be cancelled.
 * Either startDate or previousStartDate may be used to specify the event's cancelled date(s).
 * @see https://schema.org/EventCancelled
 */
class EventCancelled extends Enumeration
{

}