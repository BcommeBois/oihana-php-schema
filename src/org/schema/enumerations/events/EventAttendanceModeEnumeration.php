<?php

namespace org\schema\enumerations\events;

use org\schema\Enumeration;

/**
 * An EventAttendanceModeEnumeration value is one of potentially several modes of organising an event, relating to whether it is online or offline.
 * @see https://schema.org/EventAttendanceModeEnumeration
 */
class EventAttendanceModeEnumeration extends Enumeration
{
    /**
     * An event that is conducted as a combination of both offline and online modes.
     */
    public const string MIXED_EVENT_ATTENDANCE_MODE = 'https://schema.org/MixedEventAttendanceMode' ;

    /**
     * An event that is primarily conducted offline.
     */
    public const string OFFLINE_EVENT_ATTENDANCE_MODE = 'https://schema.org/OfflineEventAttendanceMode' ;

    /**
     * An event that is primarily conducted online.
     */
    public const string ONLINE_EVENT_ATTENDANCE_MODE = 'https://schema.org/OnlineEventAttendanceMode' ;
}