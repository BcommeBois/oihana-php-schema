<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\statistics\ObservationSeriesTrait;
use xyz\oihana\schema\constants\traits\statistics\StatisticsRecordTrait;

/**
 * The enumeration of all statistics properties constants.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
trait StatisticsTrait
{
    use ObservationSeriesTrait ,
        StatisticsRecordTrait  ;
}
