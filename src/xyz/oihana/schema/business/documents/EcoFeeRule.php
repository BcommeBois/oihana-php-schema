<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

use org\schema\CategoryCode;
use org\schema\StructuredValue;
use org\schema\Thing;
use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\EcoFeeRuleTrait;

/**
 * The calculation rule behind an environmental contribution (eco-fee) — e.g.
 * "0.25 EUR per unit for the small-electronics category, from 2026-01-01".
 *
 * A rule is a catalog/reference concept : it does not carry a monetary
 * effect by itself. Applying it on a document line is recorded by an
 * {@see AppliedEcoFee} (which references this rule and the resulting
 * amount) ; the actual monetary impact on totals always flows through an
 * {@see Adjustment} of type `environmentalFee`.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class EcoFeeRule extends StructuredValue
{
    use EcoFeeRuleTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * What this rule applies to.
     *
     * 🔑 **A category or a single item.** The name says category because that
     * is the common case, but the union accepts any {@see Thing} — a product
     * included — and a rule attached to one item is as legitimate as one
     * attached to a family. A source that publishes its rates item by item is
     * not an exception to model around ; it is the ordinary way of stating
     * that a rule concerns exactly one thing.
     *
     * A bare string names it by reference, without joining the record.
     *
     * @var null|array|string|CategoryCode|Thing
     */
    public null|array|string|CategoryCode|Thing $category ;

    /**
     * The rate this rule charges, expressed in its own unit — « 215 EUR per
     * tonne ».
     *
     * A {@see UnitPriceSpecification} and not a `MonetaryAmount` precisely so
     * the unit survives : an amount alone would say « 215 EUR » and lose the
     * tonne the whole rule turns on. A rate is charged on a physical measure —
     * a weight, a surface, a volume, a count — never on a price, so the unit is
     * not decoration : it is half the rule.
     *
     * @var null|array|UnitPriceSpecification
     * @since 1.4.0
     */
    #[HydrateAs(UnitPriceSpecification::class)]
    public null|array|UnitPriceSpecification $rate ;

    /**
     * The date from which the rule applies.
     * @var string|int|null
     */
    public null|string|int $validFrom ;

    /**
     * The date after which the rule no longer applies.
     * @var string|int|null
     */
    public null|string|int $validThrough ;
}
