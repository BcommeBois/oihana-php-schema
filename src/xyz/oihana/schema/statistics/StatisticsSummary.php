<?php

namespace xyz\oihana\schema\statistics;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\statistics\HasTradingMeasuresTrait;
use xyz\oihana\schema\constants\traits\statistics\StatisticsSummaryTrait;
use xyz\oihana\schema\traits\HasTradingMeasures;

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
 * @since   1.4.0
 */
class StatisticsSummary extends Statistics
{
    use HasTradingMeasures      ,
        HasTradingMeasuresTrait ,
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
     * @since 1.4.0
     */
    public null|int $numberOfItems ;
}
