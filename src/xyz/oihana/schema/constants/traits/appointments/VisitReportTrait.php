<?php

namespace xyz\oihana\schema\constants\traits\appointments;

/**
 * The property name constants of the {@see \xyz\oihana\schema\appointments\VisitReport} class.
 *
 * Composes {@see MeetingReportTrait}, whose constants belong to every report rather
 * than to a visit.
 *
 * @package xyz\oihana\schema\constants\traits\appointments
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
trait VisitReportTrait
{
    use MeetingReportTrait ;

    public const string MOOD    = 'mood'    ;
    public const string OUTCOME = 'outcome' ;
}
