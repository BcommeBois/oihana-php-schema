<?php

namespace xyz\oihana\schema\products;

use oihana\reflect\attributes\HydrateAs;

use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\FeeUnresolvedReason;

/**
 * A fee an item owes on top of its price — an environmental contribution, a
 * deposit, packaging, carriage.
 *
 * A {@see UnitPriceSpecification} augmented with what a fee needs and a price
 * does not : the **published rate** it derives from, and the reason it could
 * not be priced when that is the case. Everything else comes from inheritance —
 * `price` carries the amount, `unitCode`/`unitText` the unit it is charged in,
 * `priceComponentType` the nature of the fee, and `identifier`, `publisher`,
 * `url` and `description` come from `Thing`.
 *
 * 🔑 **`price` is expressed in the unit the item is billed in**, so applying it
 * takes no lookup and no conversion table :
 *
 *     amount = quantity of the line × price
 *
 * `rate` keeps the published rate beside it, in its own unit. The two coexist
 * on purpose : one computes, the other explains — the same principle an offer
 * already follows by exposing a final `price` next to its `priceComponent[]`
 * breakdown. A fee charged to a customer stays accountable without looking
 * anywhere else, and a divergence from the official rate is visible at a
 * glance.
 *
 * ⚠️ **Not to be confused with {@see ExtraPriceSpecification}**, which also
 * extends `UnitPriceSpecification` but serves price segmentation and has
 * nothing to do with fees.
 *
 * The name stays generic on purpose : `PriceComponentType` already enumerates
 * several kinds of fee, and one item may owe more than one.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.4.0
 */
class FeeSpecification extends UnitPriceSpecification
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The published rate this fee derives from, expressed in its own unit —
     * « 215 EUR per tonne » — and kept for traceability.
     *
     * A {@see UnitPriceSpecification} and not a `MonetaryAmount` precisely so
     * the unit survives : an amount alone would say « 215 EUR » and lose the
     * tonne the rate turns on.
     *
     * When the rate is already published in the unit the item is billed in, it
     * carries the same figure as `price`. When it is not, the two differ and
     * the pair is the whole point.
     *
     * @var null|array|UnitPriceSpecification
     * @since 1.4.0
     */
    #[HydrateAs(UnitPriceSpecification::class)]
    public null|array|UnitPriceSpecification $rate ;

    /**
     * Why the fee could not be priced, when it could not.
     *
     * 🔑 **Read it together with the absence of `price`.** An entry without a
     * price, carrying its `rate` and this reason, says « this is owed, here is
     * the published rate, and here is what stops us from quantifying it ».
     * Storing a zero instead would say « nothing is owed », which is false.
     *
     * @var null|string|FeeUnresolvedReason
     * @since 1.4.0
     * @see FeeUnresolvedReason Enumeration of allowed values for this property.
     */
    public null|string|FeeUnresolvedReason $unresolvedReason ;
}
