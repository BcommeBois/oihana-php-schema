<?php

namespace xyz\oihana\schema\statistics;

use oihana\reflect\attributes\HydrateAs;

use org\schema\CategoryCode;
use org\schema\QuantitativeValue;
use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\statistics\HasTradingMeasuresTrait;
use xyz\oihana\schema\constants\traits\statistics\SalesObjectivesTrait;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\people\Seller;
use xyz\oihana\schema\traits\HasTradingMeasures;

/**
 * What one salesperson is aiming at over one year.
 *
 * A {@see Statistics} record whose subject is a {@see Seller} and whose figures
 * are **targets rather than readings** : the same ten measures as every other
 * family ({@see HasTradingMeasures}), read as what should happen instead of what
 * did. It is the sentence a {@see SellerStatistics} record is compared against.
 *
 * 🔑 **The subject is the salesperson, and the narrowing is a dimension.** A
 * target set on one customer names it in {@see SalesObjectives::$assignedCustomer},
 * a target set on a range of goods names it in
 * {@see SalesObjectives::$assignedCategory} ; the two are alternatives, never both
 * at once, and a target set on the salesperson alone leaves both unset. Keeping
 * the salesperson as the subject is what lets a target and its outcome be read
 * side by side without translating one into the other.
 *
 * ⚠️ **A target is rarely as detailed as it looks.** Sources commonly publish one
 * measure — a revenue figure — and leave the nine others empty ; and where a
 * yearly target does carry a value per month, that detail is often the yearly
 * figure spread over a seasonal curve rather than twelve decisions. Neither is
 * visible in the record once written, so a reader who needs to know which one is
 * in front of them has to be told by whoever published it.
 *
 * @package xyz\oihana\schema\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class SalesObjectives extends Statistics
{
    use HasTradingMeasures      ,
        HasTradingMeasuresTrait ,
        SalesObjectivesTrait    ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The salesperson these targets are set for.
     *
     * Redeclares {@see Statistics::$about} with the same union, to name the
     * subject : a stored row is read back as a {@see Seller}, a bare code is left
     * as read.
     *
     * @var null|int|string|array|Thing
     * @since 1.5.0
     */
    #[HydrateAs(Seller::class)]
    public null|int|string|array|Thing $about ;

    /**
     * The range of goods the target is narrowed to.
     *
     * Reuses the shape {@see \org\schema\Product::$category} already carries : a
     * single code, or the ordered codes of a path through a classification, from
     * the widest level down. Unset when the target is set on a customer.
     *
     * @var null|array|string|CategoryCode|Thing
     * @since 1.5.0
     */
    public null|array|string|CategoryCode|Thing $assignedCategory ;

    /**
     * The customer the target is set on.
     *
     * A code, or the resolved customer. Unset when the target is set on a range
     * of goods.
     *
     * @var null|int|string|array|Customer
     * @since 1.5.0
     */
    public null|int|string|array|Customer $assignedCustomer ;

    /**
     * The margin rate the target is set at.
     *
     * A percentage carried as a {@see QuantitativeValue} — `value` and the
     * UN/CEFACT percent code `P1` — because a bare `25` reads as well as 25 %,
     * 2 500 % or 0.25, and the reader has no way to tell. The same shape
     * {@see \xyz\oihana\schema\traits\HasPricingMarkup::$pricingMarkup} already
     * uses for a markup band.
     *
     * 🔑 **Deliberately not one of the measures.** The ten of
     * {@see HasTradingMeasures} are amounts : they hold a run of twelve values,
     * they carry a total, and a summary adds them up term by term. A rate does
     * none of that — adding the rates of a hundred sheets answers a number that
     * is both wrong and entirely plausible. It is therefore a property of its
     * own, and a {@see StatisticsSummary} does not carry it.
     *
     * ⚠️ **What it is a rate *of* is the publisher's to say**, and the answer
     * changes the figure : a margin on the selling price and a margin on the cost
     * describe the same sale with different numbers. Nothing in the record states
     * which one is meant.
     *
     * A target with no rate set leaves the property absent — never `0`, which
     * would read as « aiming at no margin at all ».
     *
     * @var null|array|QuantitativeValue
     * @since 1.5.0
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|QuantitativeValue $marginRate ;
}
