<?php

namespace xyz\oihana\schema\products;

use oihana\reflect\attributes\HydrateAs;

use org\schema\Mass;
use org\schema\QuantitativeValue;

use xyz\oihana\schema\constants\Oihana;

/**
 * A quantity that also says what it weighs and what space it takes.
 *
 * A {@see QuantitativeValue} states how much of something there is — one
 * pallet, one box, one square meter. It does not state what that amount
 * weighs, and a packaging chain needs it at every step : a product sold by
 * the box does not weigh what the same product weighs by the piece.
 *
 * Hence this specialization, meant for the nodes of a packaging chain such as
 * {@see Product::$eligibleQuantity}, where each level — unit, package, parcel —
 * carries its own mass and its own bulk :
 *
 * ```
 * eligibleQuantity : 1 parcel — weight 15.419 kg
 *   └ valueReference : 1.403 m² — weight 10.99 kg
 * ```
 *
 * 🔑 **The ratio between two levels restates the packaging chain** — how many
 * pieces fit a box, how many boxes fit a pallet — without any of it being
 * stored twice.
 *
 * It stays a `QuantitativeValue` : anything typed on the mirror class, such as
 * `Offer::$eligibleQuantity`, accepts it unchanged, and a consumer reading only
 * `value` and `unitCode` never notices the difference.
 *
 * ⚠️ **Distinct from {@see \org\schema\Product::$weight}**, the plain weight
 * inherited from the Schema.org mirror : that one is the weight of the billed
 * unit, with no unit stated and no level attached. This class is what lets a
 * weight name the level it describes, which is exactly what a flat list of
 * weights beside the chain could never guarantee.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.4.0
 */
class PhysicalQuantity extends QuantitativeValue
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The next level of the chain — what one package holds of this level, what
     * one pallet holds of packages.
     *
     * Redeclared from {@see \org\schema\traits\ValueTrait} for the sole purpose
     * of the attribute : without it `Reflection::hydrate()` types the head of
     * the chain and leaves everything below it a raw array, so a consumer would
     * read `->weight` at the first level and `['weight']` at the second — on a
     * structure whose whole point is the ratio between two levels. The type is
     * left as `mixed`, exactly as inherited.
     *
     * ⚠️ **A decision, stated plainly** : Schema.org lets `valueReference` hold
     * things that are not quantities at all — an enumeration, a qualitative
     * value. On this class it is the next packaging level, and nothing else.
     * That is what the class exists for.
     *
     * ⚠️ The constructor assigns raw and honours no attribute : use
     * {@see \xyz\oihana\schema\helpers\hydrate\hydratePhysicalQuantity()} on
     * that path.
     *
     * @var mixed
     * @since 1.4.0
     */
    #[HydrateAs(PhysicalQuantity::class)]
    public mixed $valueReference ;

    /**
     * The space this quantity takes.
     *
     * A plain number when the unit is implicit, a {@see QuantitativeValue}
     * when it is stated (`{ value: 0.0312, unitCode: "MTQ" }`) ; an array is
     * hydrated as the latter, and sits there untouched until it is — the
     * constructor assigns raw, `#[HydrateAs]` acting through
     * `Reflection::hydrate()` alone.
     *
     * @var null|array|int|float|QuantitativeValue
     * @since 1.4.0
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue $volume ;

    /**
     * What this quantity weighs.
     *
     * The same union as
     * {@see \xyz\oihana\schema\business\documents\BusinessDocument::$weight},
     * so a weight reads the same wherever it is met : a plain number when the
     * unit is implicit, a {@see QuantitativeValue} when it is stated
     * (`{ value: 15.419, unitCode: "KGM" }`) ; an array is hydrated as the
     * latter, and sits there untouched until it is.
     *
     * @var null|array|int|float|QuantitativeValue|Mass
     * @since 1.4.0
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue|Mass $weight ;
}
