<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\enumerations\StatusEnumeration;

/**
 * What became of a meeting itself — as opposed to what became of the slot it was booked in.
 *
 * Distinct from {@see \org\schema\enumerations\events\EventStatusType}, which tracks the
 * *time slot* (scheduled, rescheduled, postponed, cancelled) and, deliberately, publishes
 * no member for « it happened ». A calendar needs to know a meeting moved ; a report needs
 * to know it took place. The two axes answer different questions and are read together :
 * a rescheduled meeting is still to come, a done one no longer moves.
 *
 * | Constant  | Description                                                            | Value                                                    |
 * |-----------|-------------------------------------------------------------------------|-----------------------------------------------------------|
 * | CANCELLED | The meeting was called off before it took place.                        | https://schema.oihana.xyz/AppointmentStatus#Cancelled   |
 * | DONE      | The meeting took place. A report may be written from here on.            | https://schema.oihana.xyz/AppointmentStatus#Done         |
 * | NO_SHOW   | The meeting was kept, and the other party was not there.                | https://schema.oihana.xyz/AppointmentStatus#NoShow       |
 * | PLANNED   | The meeting is booked and has not taken place yet.                      | https://schema.oihana.xyz/AppointmentStatus#Planned      |
 *
 * 🔑 **`NO_SHOW` is not `CANCELLED`.** A cancellation is announced and frees the time ;
 * an absence is discovered on the doorstep and costs the journey. Counting them together
 * hides the only one worth acting on.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.5.0
 */
class AppointmentStatus extends StatusEnumeration
{
    /**
     * The meeting was called off before it took place.
     */
    public const string CANCELLED = 'https://schema.oihana.xyz/AppointmentStatus#Cancelled' ;

    /**
     * The meeting took place. A report may be written from here on.
     */
    public const string DONE = 'https://schema.oihana.xyz/AppointmentStatus#Done' ;

    /**
     * The meeting was kept, and the other party was not there.
     */
    public const string NO_SHOW = 'https://schema.oihana.xyz/AppointmentStatus#NoShow' ;

    /**
     * The meeting is booked and has not taken place yet.
     */
    public const string PLANNED = 'https://schema.oihana.xyz/AppointmentStatus#Planned' ;
}
