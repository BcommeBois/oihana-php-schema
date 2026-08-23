<?php

namespace xyz\oihana\schema\enumerations;

/**
 * The meeting was kept, and the other party was not there.
 *
 * The one state that most often has something to add — the journey was made, and
 * whoever made it is the only one who can say what was found on the doorstep.
 *
 * @see AppointmentStatus::NO_SHOW
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.5.0
 */
class AppointmentNoShow extends AppointmentStatus
{
    /**
     * The URI this member states, kept towards the enumeration's own constant so the
     * two forms cannot drift apart.
     *
     * @var string|null
     * @since 1.5.0
     */
    protected static ?string $TYPE = AppointmentStatus::NO_SHOW ;
}
