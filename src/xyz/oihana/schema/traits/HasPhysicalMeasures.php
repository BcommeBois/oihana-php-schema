<?php

namespace xyz\oihana\schema\traits;

use oihana\reflect\attributes\HydrateAs;

use org\schema\Mass;
use org\schema\QuantitativeValue;

/**
 * Provides what an entry weighs and the space it takes up.
 *
 * The two travel together and are read the same way, so they are declared once
 * rather than once per composing class : a plain number when the unit is
 * implicit, a {@see QuantitativeValue} when it is stated
 * (`{ value: 537.6, unitCode: "KGM" }`) ; an array is hydrated as the latter and
 * sits there until it is — the constructor assigns raw, `#[HydrateAs]` acting
 * through {@see \oihana\reflect\Reflection::hydrate()} alone.
 *
 * On a line, the pair says what the line weighs and what it occupies : the
 * quantity multiplied by what the unit it is counted in weighs and occupies.
 * Summed over the lines, it gives the document's own values — see
 * {@see \xyz\oihana\schema\business\documents\BusinessDocument::$weight}, which
 * states the conventions in full and **stays outside this trait** : its two
 * docblocks carry a header-level argument — why neither value belongs to the
 * *monetary* `totals` — that a line cannot make.
 *
 * Composed by the document lines :
 * {@see \xyz\oihana\schema\business\documents\BusinessDocumentLine},
 * {@see \xyz\oihana\schema\business\documents\DeliveryLine} and
 * {@see \xyz\oihana\schema\business\documents\GoodsReceiptLine}. The companion
 * property name constants live next to each of them, in its own constants
 * trait, exactly as {@see HasColor} describes.
 *
 * @package xyz\oihana\schema\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
trait HasPhysicalMeasures
{
    /**
     * The space this entry takes up.
     *
     * The twin of {@see HasPhysicalMeasures::$weight}, and read the same way : a
     * plain number when the unit is implicit, a {@see QuantitativeValue} when it
     * is stated (`{ value: 1.403, unitCode: "MTQ" }`) ; an array is hydrated as
     * the latter and sits there until it is.
     *
     * On a line, the quantity multiplied by what the unit it is counted in
     * occupies. Summed over the lines, it gives the document's own volume.
     *
     * @var null|array|int|float|QuantitativeValue
     * @since 1.4.0
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue $volume ;

    /**
     * What this entry weighs.
     *
     * On a line, the quantity multiplied by what the unit it is counted in
     * weighs. Summed over the lines, it gives the document's own weight.
     *
     * A plain number carries it when the unit is implicit, a
     * {@see QuantitativeValue} when the unit is stated
     * (`{ value: 537.6, unitCode: "KGM" }`) ; an array is hydrated as the
     * latter. The same union as
     * {@see \xyz\oihana\schema\business\documents\BusinessDocument::$weight}, so
     * a weight reads the same wherever it is met.
     *
     * Deliberately neutral about gross and net. Should the distinction ever be
     * needed, it belongs to the `additionalType` of a `QuantitativeValue`, never
     * to a second property — two weights held in parallel eventually disagree.
     *
     * @var null|array|int|float|QuantitativeValue|Mass
     * @since 1.4.0
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue|Mass $weight ;
}
