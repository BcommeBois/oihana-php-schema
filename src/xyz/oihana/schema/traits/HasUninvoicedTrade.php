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
 * 💶 **What was delivered and not invoiced has a cost too** :
 * `uninvoicedCostPrice` is to `uninvoicedRevenue` what
 * {@see HasTradingMeasures::$costPrice} is to `revenue`, so the margin over what
 * was delivered reads from four runs a reader already has :
 *
 * ```
 * March, one record :
 *   revenue.values[2]               12 000   costPrice.values[2]             9 300
 *   uninvoicedRevenue.values[2]      3 500   uninvoicedCostPrice.values[2]   2 700
 *
 *   margin over what was delivered in March = ( 12 000 + 3 500 ) − ( 9 300 + 2 700 ) = 3 500
 * ```
 *
 * And a purchase cost : `uninvoicedPurchaseCost` is to `uninvoicedRevenue` what
 * {@see HasTradingMeasures::$purchaseCost} is to `revenue`, so the margin on the
 * purchase cost reads the same way, from four other runs :
 *
 * ```
 * March, one record :
 *   revenue.values[2]               12 000   purchaseCost.values[2]             9 000
 *   uninvoicedRevenue.values[2]      3 500   uninvoicedPurchaseCost.values[2]   2 600
 *
 *   margin on the purchase cost over what was delivered in March = ( 12 000 + 3 500 ) − ( 9 000 + 2 600 ) = 3 900
 * ```
 *
 * The backlog has no such series : what is ordered is not delivered yet, and its
 * cost can still move before it is.
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
     * The cost price of what was delivered and not yet invoiced, month by month —
     * the cost of {@see HasUninvoicedTrade::$uninvoicedRevenue}, filed under the
     * same month.
     *
     * Read beside {@see HasTradingMeasures::$costPrice} as `uninvoicedRevenue`
     * is read beside `revenue` : the two never overlap, and what was delivered in
     * a month cost their sum. No margin series goes with it — a reader subtracts
     * the two runs it already has.
     *
     * ⚠️ **Absent is not zero.** A record may carry `uninvoicedRevenue` without
     * its cost, when its source cannot price what was delivered. A reader then
     * has no margin to show for that record : reading the missing cost as zero
     * would turn the whole revenue into margin.
     *
     * Transitional, and a run only — see {@see HasUninvoicedTrade}.
     *
     * @var null|array|ObservationSeries
     * @since 1.5.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $uninvoicedCostPrice ;

    /**
     * The purchase cost of what was delivered and not yet invoiced, month by
     * month — the purchase cost of {@see HasUninvoicedTrade::$uninvoicedRevenue},
     * filed under the same month.
     *
     * Read beside {@see HasTradingMeasures::$purchaseCost} as `uninvoicedRevenue`
     * is read beside `revenue` : the two never overlap, and what was delivered in
     * a month was bought for their sum. No margin series goes with it — a reader
     * subtracts the two runs it already has.
     *
     * ⚠️ **Absent is not zero.** A record may carry `uninvoicedRevenue` without
     * its purchase cost, when its source cannot price what was delivered, or
     * when its reader is not allowed to see a purchase cost. A reader then has no
     * margin on the purchase cost to show for that record : reading the missing
     * cost as zero would turn the whole revenue into margin.
     *
     * Transitional, and a run only — see {@see HasUninvoicedTrade}.
     *
     * @var null|array|ObservationSeries
     * @since 1.5.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $uninvoicedPurchaseCost ;

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
