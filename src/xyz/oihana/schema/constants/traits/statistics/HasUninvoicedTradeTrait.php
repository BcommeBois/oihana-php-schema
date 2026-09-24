<?php

namespace xyz\oihana\schema\constants\traits\statistics;

/**
 * The property name constants of the {@see \xyz\oihana\schema\traits\HasUninvoicedTrade} trait.
 *
 * @package xyz\oihana\schema\constants\traits\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
trait HasUninvoicedTradeTrait
{
    const string ORDER_BACKLOG         = 'orderBacklog'        ;
    const string UNINVOICED_COST_PRICE = 'uninvoicedCostPrice' ;
    const string UNINVOICED_REVENUE    = 'uninvoicedRevenue'   ;
}
