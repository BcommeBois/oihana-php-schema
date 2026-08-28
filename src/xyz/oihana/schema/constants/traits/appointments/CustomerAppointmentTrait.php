<?php

namespace xyz\oihana\schema\constants\traits\appointments;

/**
 * The property name constants of the {@see \xyz\oihana\schema\appointments\CustomerAppointment} class.
 *
 * Composes {@see AppointmentTrait}, whose constants belong to every appointment
 * rather than to a customer one.
 *
 * @package xyz\oihana\schema\constants\traits\appointments
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
trait CustomerAppointmentTrait
{
    use AppointmentTrait ;

    public const string ASSIGNED_SELLER = 'assignedSeller' ;
}
