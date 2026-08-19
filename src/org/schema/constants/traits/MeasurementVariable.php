<?php

namespace org\schema\constants\traits;

/**
 * The measurement variable properties enumeration.
 *
 * These terms are shared by every type that describes what a figure measures
 * — {@see \org\schema\StatisticalVariable} and {@see \org\schema\Observation} —
 * and are named here once, as they are declared once in
 * {@see \org\schema\traits\MeasurementVariableTrait}. The technique and its
 * method, which that trait composes rather than declares, are named by
 * {@see MeasurementTechnique}.
 */
trait MeasurementVariable
{
    const string MEASURED_PROPERTY       = 'measuredProperty'       ;
    const string MEASUREMENT_DENOMINATOR = 'measurementDenominator' ;
    const string MEASUREMENT_QUALIFIER   = 'measurementQualifier'   ;
}
