<?php

namespace xyz\oihana\schema\traits;

use org\schema\QuantitativeValue;

use oihana\reflect\attributes\HydrateAs;

/**
 * Provides an optional pricing markup band for a schema entity.
 *
 * This is a **house** property — a commercial guardrail layered on top of the
 * harvested data. It is not a price effect : it never computes a price, it only
 * bounds what a seller may quote. The effects themselves (discounts, surcharges,
 * segment substitutions, fixed prices) stay on {@see PricingCondition}.
 *
 * It is extracted into a dedicated trait so it can be composed only by the
 * entities that carry a pricing policy (e.g. {@see ProductCategoryTerm}) without leaking
 * into the flat thesaurus families — mirroring {@see HasColor} and {@see HasTreeMetrics}.
 *
 * @package xyz\oihana\schema\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.4.0
 */
trait HasPricingMarkup
{
    /**
     * The markup band applied on top of a base price, carried as a {@see QuantitativeValue} :
     * - `minValue`       — the floor : the lowest acceptable markup ;
     * - `maxValue`       — the ceiling, when the markup is also capped ;
     * - `value`          — the recommended (target) markup ;
     * - `unitCode`       — the unit, e.g. `'P1'` (the UN/CEFACT percent code) ;
     * - `valueReference` — the base the markup applies to, e.g. {@see PriceType::COGS}.
     *
     * The markup is expressed **on the base**, not on the selling price :
     * `sellingPrice >= base × (1 + minValue / 100)`. An 8 % markup over a cost of
     * 100 is a selling price of 108 — it is *not* an 8 % margin on the selling
     * price, which would be 108.70. The same calculation is already named
     * *margin* by {@see ProductProviderInfo::$buyingPriceMargin} (whose base is the buying price, not the cost).
     *
     * Example:
     * ```php
     * $term->pricingMarkup = new QuantitativeValue
     * ([
     *     'minValue'       => 8 ,
     *     'unitCode'       => 'P1' ,
     *     'valueReference' => PriceType::COGS ,
     * ]);
     * ```
     *
     * @var QuantitativeValue|array|null
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|QuantitativeValue $pricingMarkup ;
}
