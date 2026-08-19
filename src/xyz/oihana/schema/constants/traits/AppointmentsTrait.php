<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\appointments\CustomerAppointmentTrait;
use xyz\oihana\schema\constants\traits\appointments\FollowUpTrait;
use xyz\oihana\schema\constants\traits\appointments\VisitReportTrait;

/**
 * Aggregates the property name constants of the `xyz\oihana\schema\appointments` namespace.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
trait AppointmentsTrait
{
    use CustomerAppointmentTrait ,
        FollowUpTrait    ,
        VisitReportTrait ;
}
