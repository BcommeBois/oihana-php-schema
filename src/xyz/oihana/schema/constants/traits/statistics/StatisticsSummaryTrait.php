<?php

namespace xyz\oihana\schema\constants\traits\statistics;

/**
 * The property name constants of the {@see \xyz\oihana\schema\statistics\StatisticsSummary} class.
 *
 * `OBSERVATION_DATE` already exists in other constants traits with the same
 * value — on the receivables record, and on the Schema.org observation. PHP
 * accepts a constant declared twice through traits as long as the declarations
 * agree ; a test asserts that they do, so that a drift fails there rather than
 * at class loading.
 *
 * @package xyz\oihana\schema\constants\traits\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
trait StatisticsSummaryTrait
{
    const string NUMBER_OF_ITEMS  = 'numberOfItems'   ;
    const string OBSERVATION_DATE = 'observationDate' ;
}
