<?php

namespace xyz\oihana\schema\statistics;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\statistics\HasReceivablesTrait;
use xyz\oihana\schema\constants\traits\statistics\HasTradingMeasuresTrait;
use xyz\oihana\schema\constants\traits\statistics\HasUninvoicedTradeTrait;
use xyz\oihana\schema\constants\traits\statistics\StatisticsSummaryTrait;
use xyz\oihana\schema\traits\HasReceivables;
use xyz\oihana\schema\traits\HasTradingMeasures;
use xyz\oihana\schema\traits\HasUninvoicedTrade;

/**
 * Several records, added together.
 *
 * A {@see Statistics} whose figures are not one counterparty's but the **sum of
 * a selection** — a portfolio over a year, a branch, a range of goods. It
 * carries the same ten measures as any record ({@see HasTradingMeasures}), each
 * summed **term by term** : the January of the summary is the sum of the
 * Januaries, the February the sum of the Februaries, and so on over the twelve
 * positions. That is what lets a reader draw the monthly curve of a *set*.
 *
 * It also carries the three series of the trade that is not invoiced yet
 * ({@see HasUninvoicedTrade}), which a {@see SellerStatistics} record may hold
 * beside the ten measures, summed the same way. 🚨 **They are declared here so
 * that a summary does not lose them** : the constructor keeps only the
 * properties a class declares and drops the others without a word, so a sum of
 * salespeople's records built without them would come out with their
 * `uninvoicedRevenue`, `uninvoicedCostPrice` and `orderBacklog` gone. A summary
 * of any other family leaves them unset, and they are not serialized.
 *
 * ⚠️ **A summed `uninvoicedCostPrice` only prices the records that carried
 * one.** When some of the summed records hold `uninvoicedRevenue` without its
 * cost, the summed cost falls short of the summed revenue, and a margin read
 * from the two comes out too high. Whoever builds the summary sums only records
 * that carry both, or says it did not.
 *
 * And it carries what is owed and late ({@see HasReceivables}), which a
 * {@see CustomerReceivables} record holds instead of the ten measures, declared
 * here for the same reason. The amounts sum term by term — the overdue of a
 * portfolio is the sum of its customers' —, and so does `numberOfDocuments` ;
 * 🚨 **`daysLate` does not** : ten customers ninety days late are not nine
 * hundred days late. Whoever builds the summary takes the largest, or leaves it
 * out, and says which.
 *
 * 🔑 **And a summary of such records is dated, as they are.** A receivables
 * record is a snapshot — true on its {@see CustomerReceivables::$observationDate}
 * and on no other day —, so a sum of them is a snapshot too : it carries the
 * same date under the same name ({@see StatisticsSummary::$observationDate})
 * and no {@see Statistics::$year}, which stays unset and is not serialized. A
 * summary of a year of trade does the opposite : a year, and no reading date.
 * Declared here for the reason every other key is : a date the class does not
 * declare would be dropped by the constructor, and the summary would not say
 * which day it was read on.
 *
 * 🔑 **One class for every family, because a summary loses the only thing that
 * told them apart.** {@see CustomerStatistics} and {@see ProviderStatistics}
 * differ by their subject and by the dimensions a subject carries ; the ten
 * measures are the same on both sides, and three of the six families add nothing
 * at all. Writing one summary class per family would produce classes identical
 * down to the last property.
 *
 * And the reader still knows which side of the trade is being read :
 * {@see Statistics::$direction} is inherited and stays true — a sum of sales is a
 * sale.
 *
 * 🚨 **`about` has two states, and the difference is the whole contract.**
 *
 * - **Absent** when the summary answers for a plain selection : it is about no
 *   one, and saying `null` would claim the subject is unknown where the truth is
 *   that the question does not apply. The property is inherited and simply never
 *   assigned — declared without a default, it stays uninitialized, and the
 *   serialization skips it.
 * - **Set to the grouping key** when the selection was grouped by a dimension :
 *   a summary grouped by point of sale *is* « the figures of warehouse 400 », and
 *   that warehouse is its subject. The inherited union already accepts it.
 *
 * ⚠️ There is only **one** `about`, so a grouping over two dimensions at once has
 * nowhere to say so. That is a design question the day it is needed, not an
 * oversight.
 *
 * ⚠️ **This class stores a result ; it computes nothing.** The summing belongs to
 * whoever reads the records — the library only models the shape, as it does for
 * {@see \xyz\oihana\schema\business\documents\AgingSummary}.
 *
 * ```php
 * $summary = new StatisticsSummary
 * ([
 *     StatisticsSummary::YEAR            => 2024 ,
 *     StatisticsSummary::DIRECTION       => BusinessDocumentDirection::SALE ,
 *     StatisticsSummary::NUMBER_OF_ITEMS => 128 ,
 *     StatisticsSummary::REVENUE         => new ObservationSeries
 *     ([
 *         Oihana::UNIT_CODE => 'EUR' ,
 *         Oihana::VALUE     => 4820.50 ,                 // the total of the summed totals
 *         Oihana::VALUES    => [ 401.20 , 388.00 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 , 0 ] ,
 *     ]) ,
 * ]) ;
 *
 * $summary->about ?? null ; // null — a summary of a plain selection is about no one
 * ```
 *
 * @package xyz\oihana\schema\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class StatisticsSummary extends Statistics
{
    use HasReceivables          ,
        HasReceivablesTrait     ,
        HasTradingMeasures      ,
        HasTradingMeasuresTrait ,
        HasUninvoicedTrade      ,
        HasUninvoicedTradeTrait ,
        StatisticsSummaryTrait  ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * How many records were summed to produce this line.
     *
     * 🚨 **It counts records, not counterparties.** A source that writes one
     * record per counterparty **per year and per operating company** makes a
     * counterparty traded with by two companies weigh two records — and this
     * property counts it twice. A reader captioning it « customers » rather than
     * « records » states a number that was never measured.
     *
     * Reuses the name schema.org gives the same idea on
     * {@see \org\schema\ItemList::$numberOfItems}, without the list itself : a
     * summary has melted its members, it does not enumerate them.
     *
     * @var int|null
     * @since 1.5.0
     */
    public null|int $numberOfItems ;

    /**
     * The date the summed records were read at, as an ISO 8601 date (`2026-09-22`).
     *
     * Set on a summary of records that are snapshots ({@see CustomerReceivables}),
     * whose figures are true on one day and on no other : their sum is true on
     * that same day. Left unset on a summary of a year of trade, which is dated by
     * {@see Statistics::$year} instead — the two never coexist, and neither is
     * serialized when unset.
     *
     * Same term, same format as {@see CustomerReceivables::$observationDate} and
     * {@see \org\schema\Observation::$observationDate}.
     *
     * @var null|string
     * @since 1.5.0
     */
    public null|string $observationDate ;
}
