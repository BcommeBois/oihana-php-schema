<?php

namespace org\schema\constants\traits;

/**
 * The Observation properties enumeration.
 *
 * The measurement terms an Observation also carries are named by
 * {@see MeasurementVariable}, which it shares with {@see StatisticalVariable}.
 */
trait Observation
{
    const string MARGIN_OF_ERROR    = 'marginOfError'     ;
    const string OBSERVATION_ABOUT  = 'observationAbout'  ;
    const string OBSERVATION_DATE   = 'observationDate'   ;
    const string OBSERVATION_PERIOD = 'observationPeriod' ;
}
