<?php

namespace xyz\oihana\schema\traits;

use oihana\reflect\attributes\HydrateAs;

use xyz\oihana\schema\statistics\ObservationSeries;

/**
 * The trade that is not invoiced yet.
 *
 * Two stages of a sale that {@see HasTradingMeasures::$revenue} cannot see, since
 * it counts what was invoiced : what was delivered and is still to invoice
 * (`uninvoicedRevenue`), and what is ordered and still to deliver
 * (`orderBacklog`). Read beside `revenue`, they tell how the trade of a month
 * stands before its invoices are all out.
 *
 * 🔑 **The three never overlap, and that is why they add up.** A sale sits in one
 * of them at a time and moves along as it goes — ordered, then delivered, then
 * invoiced. On a record stepped month by month (`P1M`) :
 *
 * ```
 * March, one record :
 *   revenue.values[2]            12 000   invoiced in March
 *   uninvoicedRevenue.values[2]   3 500   delivered in March, not invoiced yet
 *   orderBacklog.values[2]        4 200   ordered, delivery planned in March, not delivered yet
 *
 *   delivered in March               = 12 000 + 3 500 = 15 500
 *   delivered or to deliver in March = 15 500 + 4 200 = 19 700
 * ```
 *
 * On 2 April, March is invoiced : `uninvoicedRevenue.values[2]` falls to 0 — or
 * the series goes altogether — and `revenue.values[2]` rises to 15 500. What was
 * delivered in March has not moved ; it has changed column.
 *
 * ⚠️ **Both are transitional.** Their amounts are on their way to `revenue`, and
 * a record whose trade is all invoiced carries no such series at all : absent,
 * not twelve zeros — zeros would state that something was measured and found
 * empty.
 *
 * ⚠️ **Both are runs only** : `values`, never `value`. A total over the year would
 * add months that each stand at a different stage of their invoicing, and read
 * as a figure of the year it is not.
 *
 * They are not among the ten measures of {@see HasTradingMeasures} : those hold
 * for every family, while a stage of a sale belongs to the record of whoever
 * made it. A family composes this trait when it has such stages to carry.
 *
 * A property is typed `null|array|ObservationSeries` for the same reason as the
 * ten measures : the union names a single class, so
 * {@see \oihana\reflect\Reflection::hydrate()} resolves a stored row on its own.
 *
 * @package xyz\oihana\schema\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
trait HasUninvoicedTrade
{
    /**
     * What is ordered and not yet delivered, filed under the month the delivery
     * is planned for.
     *
     * Not a revenue : nothing of it is delivered, and a planned date can move.
     * Not the quotes either : an order is an offer the customer has accepted. A
     * month may lie in the future — the backlog is the trade still to come — and
     * a past month may still hold some : a delivery planned for it that has not
     * happened.
     *
     * Transitional, and a run only — see {@see HasUninvoicedTrade}.
     *
     * @var null|array|ObservationSeries
     * @since 1.5.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $orderBacklog ;

    /**
     * What was delivered and not yet invoiced, month by month.
     *
     * Not the invoiced trade : that is {@see HasTradingMeasures::$revenue}, and the
     * two never overlap — what was delivered in a month is their sum. Not what is
     * ordered and still to deliver : that is
     * {@see HasUninvoicedTrade::$orderBacklog}.
     *
     * A transitional run : once the month is invoiced its amounts leave this
     * series for `revenue`, and a record whose deliveries are all invoiced
     * carries no such series at all — absent, not twelve zeros. A run only : no
     * yearly `value`.
     *
     * @var null|array|ObservationSeries
     * @since 1.5.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $uninvoicedRevenue ;
}
