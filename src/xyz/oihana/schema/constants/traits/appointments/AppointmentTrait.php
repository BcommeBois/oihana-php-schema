<?php

namespace xyz\oihana\schema\constants\traits\appointments;

/**
 * The property name constants of the {@see \xyz\oihana\schema\appointments\Appointment} class.
 *
 * @package xyz\oihana\schema\constants\traits\appointments
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
trait AppointmentTrait
{
    public const string APPOINTMENT_STATUS = 'appointmentStatus' ;
    public const string APPOINTMENT_TYPE   = 'appointmentType'   ;
    public const string ASSIGNED_COMPANY   = 'assignedCompany'   ;
    public const string REPORT             = 'report'            ;
    public const string TAGS               = 'tags'              ;
}
