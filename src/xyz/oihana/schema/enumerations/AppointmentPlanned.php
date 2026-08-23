<?php

namespace xyz\oihana\schema\enumerations;

/**
 * The meeting is booked and has not taken place yet.
 *
 * The state a meeting is born in. The object form is there for the day a plan carries
 * a note of its own ; the bare constant is enough for the rest.
 *
 * @see AppointmentStatus::PLANNED
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.5.0
 */
class AppointmentPlanned extends AppointmentStatus
{
    /**
     * The URI this member states, kept towards the enumeration's own constant so the
     * two forms cannot drift apart.
     *
     * @var string|null
     * @since 1.5.0
     */
    protected static ?string $TYPE = AppointmentStatus::PLANNED ;
}
