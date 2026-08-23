<?php

namespace xyz\oihana\schema\enumerations;

/**
 * The meeting was called off before it took place.
 *
 * The member class rather than the bare constant when the cancellation has a reason
 * worth keeping — who called it off, and why — carried in the inherited `description`.
 *
 * @see AppointmentStatus::CANCELLED
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.5.0
 */
class AppointmentCancelled extends AppointmentStatus
{
    /**
     * The URI this member states, kept towards the enumeration's own constant so the
     * two forms cannot drift apart.
     *
     * @var string|null
     * @since 1.5.0
     */
    protected static ?string $TYPE = AppointmentStatus::CANCELLED ;
}
