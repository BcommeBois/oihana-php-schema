<?php

namespace xyz\oihana\schema\products;

use oihana\reflect\attributes\HydrateAs;

use oihana\reflect\attributes\HydrateWith;
use org\schema\PropertyValue;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\constants\Oihana;

/**
 * A conditional pricing rule : a discount (or a tariff substitution) granted to
 * a scoped set of buyers on a scoped set of items, valid over a period.
 *
 * A condition is a catalog/reference concept — the twin, on the sell side, of a
 * provider buying condition — resolved most-specific-first for a given
 * (customer, item, place) context by reading its {@see PricingConditionSelector}.
 * It carries exactly one effect :
 * - an {@see Adjustment} (a signed percentage or amount — a discount, or a
 *   surcharge when negative), or
 * - a `substitutesSegment` ({@see PriceSegmentation}) that swaps the buyer's
 *   usual tariff segment for another (applied *instead of* a discount).
 *
 * `excludedCustomers` / `excludedProducts` carve exceptions out of the scope
 * (e.g. "−10 % for this group, except these two customers"). `quantityDiscount`
 * holds an optional quantity-tier effect. All effect and exclusion fields are
 * optional : a condition with none is informational only.
 *
 * @package xyz\oihana\schema\products
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
class PricingCondition extends StructuredValue
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Additional, not-yet-modelled properties carried structurally.
     * @var array|PropertyValue|null
     */
    #[HydrateWith( PropertyValue::class ) ]
    public null|array|PropertyValue $additionalProperty = null ;

    /**
     * The price adjustment granted (a signed percentage or amount). Mutually
     * exclusive with `substitutesSegment`.
     * @var Adjustment|array|null
     */
    #[HydrateAs(Adjustment::class)]
    public null|array|Adjustment $adjustment ;

    /**
     * The identifiers of the customers carved out of the scope (exceptions).
     * @var array|null
     */
    public ?array $excludedCustomers ;

    /**
     * The identifiers of the products carved out of the scope (exceptions).
     * @var array|null
     */
    public ?array $excludedProducts ;

    /**
     * An optional quantity-tier effect (discounts by quantity).
     * @var PriceQuantityDiscount|array|null
     */
    #[HydrateAs(PriceQuantityDiscount::class)]
    public null|array|PriceQuantityDiscount $quantityDiscount ;

    /**
     * The selection criteria (who / what / where) this condition applies to.
     * @var PricingConditionSelector|array|null
     */
    #[HydrateAs(PricingConditionSelector::class)]
    public null|array|PricingConditionSelector $selector ;

    /**
     * The tariff segment substituted for the buyer's usual one, applied
     * *instead of* a discount. Mutually exclusive with `adjustment`.
     * @var PriceSegmentation|array|null
     */
    #[HydrateAs(PriceSegmentation::class)]
    public null|array|PriceSegmentation $substitutesSegment ;

    /**
     * The date from which the condition applies.
     * @var string|int|null
     */
    public null|string|int $validFrom ;

    /**
     * The date after which the condition no longer applies.
     * @var string|int|null
     */
    public null|string|int $validThrough ;
}
