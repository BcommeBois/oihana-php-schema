<?php

namespace org\schema\constants\traits;

/**
 * The StatisticalVariable properties enumeration.
 *
 * The measurement terms a StatisticalVariable also carries are named by
 * {@see MeasurementVariable}, which it shares with {@see Observation}.
 */
trait StatisticalVariable
{
    const string POPULATION_TYPE = 'populationType' ;
    const string STAT_TYPE       = 'statType'       ;
}
