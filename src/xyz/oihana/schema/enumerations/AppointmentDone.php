<?php

namespace xyz\oihana\schema\enumerations;

/**
 * The meeting took place. A report may be written from here on.
 *
 * Rarely needs the object form : what there is to say about a meeting that happened
 * belongs to its report. Declared all the same, so the four states are read the same way.
 *
 * @see AppointmentStatus::DONE
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.5.0
 */
class AppointmentDone extends AppointmentStatus
{
    /**
     * The URI this member states, kept towards the enumeration's own constant so the
     * two forms cannot drift apart.
     *
     * @var string|null
     * @since 1.5.0
     */
    protected static ?string $TYPE = AppointmentStatus::DONE ;
}
