<?php

namespace xyz\oihana\schema\traits;

use oihana\reflect\attributes\HydrateAs;

use xyz\oihana\schema\statistics\ObservationSeries;

/**
 * The ten measures a body of trading figures is made of.
 *
 * What was sold and what it earned (`revenue`), what that trade was worth at
 * each of the three costs a merchant keeps — the price paid (`purchaseCost`),
 * the weighted average cost (`averageCost`) and the loaded cost price
 * (`costPrice`) —, the margin over each of them (`purchaseMargin`,
 * `averageMargin`, `grossMargin`), and what physically moved (`quantity`,
 * `volume`, `weight`).
 *
 * They are declared once rather than once per family : a customer, a supplier,
 * an article and a company are measured by the same ten figures, and ten
 * properties written out four times are forty chances to drift. A family
 * composes the trait and adds only what is its own — who the figures are about,
 * and the dimensions that qualify them.
 *
 * 🔑 **A measure carries what its source states, and nothing else.** Each is an
 * {@see ObservationSeries} : `value` for the total over the period, `values` for
 * the run behind it, and either may be absent. A margin published once a year
 * has no monthly detail to give ; a cost published month by month was never
 * totalled. **Neither absence is filled in** — a summed total and a derived
 * monthly figure are indistinguishable from published ones once written, and a
 * reader has no way back. Summing, dividing, and turning a margin into a rate
 * belong to whoever displays the figures, and stay visible there.
 *
 * ⚠️ **Six of the ten are confidential by nature.** The three costs and the
 * three margins say what an operator earns on a given counterparty : they are
 * the figures a customer must never read about itself, and an application
 * serving them owes them a permission of their own. Hiding them from a
 * projection is not enough on its own — sorting, filtering, faceting and
 * grouping reconstruct a figure as surely as reading it. The library states the
 * shape ; the guard belongs to the consumer, which alone knows its readers.
 *
 * A measure is typed `null|array|ObservationSeries` : the union names a single
 * class, so {@see \oihana\reflect\Reflection::hydrate()} resolves a stored row on
 * its own, and the `array` is where that row sits until it does — the
 * constructor assigns raw, `#[HydrateAs]` acting through `hydrate()` alone.
 *
 * @package xyz\oihana\schema\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
trait HasTradingMeasures
{
    /**
     * The trade valued at the weighted average cost of what was sold.
     *
     * The middle of the three costs : what the goods stood at in inventory when
     * they left it, rather than what the last purchase order paid
     * ({@see HasTradingMeasures::$purchaseCost}) or what they end up costing
     * once the charges around them are loaded on
     * ({@see HasTradingMeasures::$costPrice}).
     *
     * @var null|array|ObservationSeries
     * @since 1.4.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $averageCost ;

    /**
     * The margin over {@see HasTradingMeasures::$averageCost}.
     *
     * @var null|array|ObservationSeries
     * @since 1.4.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $averageMargin ;

    /**
     * The trade valued at cost price — the purchase price with the charges that
     * ride along with it loaded on.
     *
     * The cost a margin is read against when the question is whether the trade
     * paid for itself, rather than whether it was bought well.
     *
     * @var null|array|ObservationSeries
     * @since 1.4.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $costPrice ;

    /**
     * The margin over {@see HasTradingMeasures::$costPrice} — the gross margin.
     *
     * The one a trading business steers by : what the trade earned once the
     * goods it consumed are counted at what they truly cost.
     *
     * @var null|array|ObservationSeries
     * @since 1.4.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $grossMargin ;

    /**
     * The trade valued at the price paid for the goods.
     *
     * @var null|array|ObservationSeries
     * @since 1.4.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $purchaseCost ;

    /**
     * The margin over {@see HasTradingMeasures::$purchaseCost}.
     *
     * @var null|array|ObservationSeries
     * @since 1.4.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $purchaseMargin ;

    /**
     * How much was traded, in the units the items are counted in.
     *
     * ⚠️ **Homogeneous only within one item.** A figure spanning several items
     * adds square metres to cubic metres to pieces ; it is worth reading per
     * article, and worth distrusting as a total. {@see HasTradingMeasures::$volume}
     * and {@see HasTradingMeasures::$weight} are the two that add up whatever was
     * sold.
     *
     * @var null|array|ObservationSeries
     * @since 1.4.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $quantity ;

    /**
     * What the trade earned — the amount invoiced.
     *
     * @var null|array|ObservationSeries
     * @since 1.4.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $revenue ;

    /**
     * The space the traded goods took up.
     *
     * @var null|array|ObservationSeries
     * @since 1.4.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $volume ;

    /**
     * What the traded goods weighed.
     *
     * @var null|array|ObservationSeries
     * @since 1.4.0
     */
    #[HydrateAs(ObservationSeries::class)]
    public null|array|ObservationSeries $weight ;
}
