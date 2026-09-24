<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\statistics\CustomerReceivablesTrait;
use xyz\oihana\schema\constants\traits\statistics\CustomerStatisticsTrait;
use xyz\oihana\schema\constants\traits\statistics\HasReceivablesTrait;
use xyz\oihana\schema\constants\traits\statistics\HasTradingMeasuresTrait;
use xyz\oihana\schema\constants\traits\statistics\HasUninvoicedTradeTrait;
use xyz\oihana\schema\constants\traits\statistics\ObservationSeriesTrait;
use xyz\oihana\schema\constants\traits\statistics\SalesObjectivesTrait;
use xyz\oihana\schema\constants\traits\statistics\SellerStatisticsTrait;
use xyz\oihana\schema\constants\traits\statistics\StatisticsRecordTrait;
use xyz\oihana\schema\constants\traits\statistics\StatisticsSummaryTrait;

/**
 * The enumeration of all statistics properties constants.
 *
 * @package xyz\oihana\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
trait StatisticsTrait
{
    use CustomerReceivablesTrait ,
        CustomerStatisticsTrait  ,
        HasReceivablesTrait      ,
        HasTradingMeasuresTrait  ,
        HasUninvoicedTradeTrait  ,
        ObservationSeriesTrait   ,
        SalesObjectivesTrait     ,
        SellerStatisticsTrait    ,
        StatisticsRecordTrait    ,
        StatisticsSummaryTrait   ;
}
