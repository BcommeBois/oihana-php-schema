<?php

namespace xyz\oihana\schema\traits;

use oihana\reflect\attributes\HydrateAs;

use xyz\oihana\schema\statistics\ObservationSeries;

/**
 * What is owed and late.
 *
 * The part of a customer's account that is past its due date, read at one date :
 * the amount itself (`overdue`), the same amount split by how long it has been
 * late (four buckets), the part of it booked on doubtful accounts (`doubtful`),
 * how late the oldest piece is (`daysLate`) and how many pieces make it up
 * (`numberOfDocuments`). Nothing not yet due, nothing settled : a record that
 * carries these figures says what is late, and only that.
 *
 * 🔑 **The four buckets add up to `overdue`, and nothing else does.** A piece
 * sits in exactly one bucket, decided by its due date and the date the figures
 * were read at. On a record read on 22 September :
 *
 * ```
 * Vialaret Joinery, read on 22 September :
 *   overdue1To30.value        3 100    due 21 days ago
 *   overdue31To60.value           0
 *   overdue61To90.value       5 400    due 62 days ago
 *   overdueOver90.value       2 650    due 98 days ago
 *   overdue.value            11 150    = 3 100 + 0 + 5 400 + 2 650
 *   doubtful.value            2 650    the oldest piece, booked on a doubtful account
 *   daysLate.value               98    the oldest due date passed
 *   numberOfDocuments             3    three pieces past due
 * ```
 *
 * ⚠️ **These figures are a snapshot, not a series.** They are true at the date
 * they were read and at no other : a payment recorded the next morning changes
 * every one of them. A new reading **replaces** the previous one — nothing is
 * ever added across time —, and the date it was read at travels with it, on
 * whatever holds the trait ({@see \xyz\oihana\schema\statistics\CustomerReceivables::$observationDate}).
 * For the same reason every measure carries a `value` and never a `values` :
 * a snapshot has no months.
 *
 * ⚠️ **Absent means « nothing of that kind », never zero.** A record with no
 * `doubtful` has nothing booked on a doubtful account ; a record with no
 * `daysLate` has nothing past due — and then it should not exist at all, since
 * these figures describe what is late. A zero would state that something was
 * measured and found empty.
 *
 * ⚠️ **`daysLate` does not add up.** The amounts sum term by term across records
 * — the overdue of a portfolio is the sum of its customers' —, but the days do
 * not : ten customers ninety days late are not nine hundred days late. Whoever
 * summarizes several records takes the largest, or leaves it out, and says which.
 *
 * The amounts are those of the books, tax included, as the books state them : a
 * salesperson collecting a payment needs the figure the customer actually owes.
 *
 * They are not among the ten measures of {@see HasTradingMeasures} : those
 * describe a year of trade, cut month by month ; this describes a debt at one
 * date, with no month at all. A family composes this trait when it has such a
 * debt to carry.
 *
 * A property is typed `null|array|ObservationSeries` for the same reason as the
 * ten measures : the union names a single class, so
 * {@see \oihana\reflect\Reflection::hydrate()} resolves a stored row on its own.
 *
 * @package xyz\oihana\schema\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
trait HasReceivables
{
    /**
     * How late the oldest piece past due is, in days, at the date the figures
     * were read.
     *
     * One figure for the whole record — the longest wait, not an average —,
     * with the UN/CEFACT day code `DAY` as its unit. Absent when nothing is past
     * due. A maximum, never a sum : see {@see HasReceivables}.
     *
     * @var null|array|ObservationSeries
     * @since 1.5.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $daysLate ;

    /**
     * The part of {@see HasReceivables::$overdue} booked on doubtful accounts —
     * the pieces the books have moved out of the customer's ordinary account
     * because their recovery is in doubt.
     *
     * A part of `overdue`, never an addition to it : `doubtful ≤ overdue`. Absent
     * when nothing is booked that way.
     *
     * @var null|array|ObservationSeries
     * @since 1.5.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $doubtful ;

    /**
     * How many pieces past due make up {@see HasReceivables::$overdue}, credit
     * notes included.
     *
     * A plain count, not a measure : it has no unit and no series, and it adds up
     * across records the way {@see \xyz\oihana\schema\statistics\StatisticsSummary::$numberOfItems}
     * counts records. It tells one large invoice from fifteen small ones, which
     * are not chased the same way.
     *
     * @var null|int
     * @since 1.5.0
     */
    public null|int $numberOfDocuments ;

    /**
     * What is owed and past its due date, at the date the figures were read :
     * the sum of the balances of the pieces not settled whose due date has
     * passed, credit notes deducted.
     *
     * Not what is due later, not what a bill of exchange awaiting acceptance
     * covers, not what is settled. The sum of the four buckets, to the cent.
     *
     * @var null|array|ObservationSeries
     * @since 1.5.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $overdue ;

    /**
     * The part of {@see HasReceivables::$overdue} due between 1 and 30 days ago.
     *
     * @var null|array|ObservationSeries
     * @since 1.5.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $overdue1To30 ;

    /**
     * The part of {@see HasReceivables::$overdue} due between 31 and 60 days ago.
     *
     * @var null|array|ObservationSeries
     * @since 1.5.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $overdue31To60 ;

    /**
     * The part of {@see HasReceivables::$overdue} due between 61 and 90 days ago.
     *
     * @var null|array|ObservationSeries
     * @since 1.5.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $overdue61To90 ;

    /**
     * The part of {@see HasReceivables::$overdue} due more than 90 days ago.
     *
     * @var null|array|ObservationSeries
     * @since 1.5.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $overdueOver90 ;
}
