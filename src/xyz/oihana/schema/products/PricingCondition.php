<?php

namespace xyz\oihana\schema\products;

use oihana\reflect\attributes\HydrateAs;

use oihana\reflect\attributes\HydrateWith;
use org\schema\DefinedTerm;
use org\schema\MonetaryAmount;
use org\schema\PropertyValue;
use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\organizations\Provider;
use xyz\oihana\schema\organizations\Subsidiary;
use xyz\oihana\schema\places\Warehouse;

/**
 * A conditional pricing rule : a discount (or a tariff substitution) granted to
 * a scoped set of buyers on a scoped set of items, valid over a period.
 *
 * A condition is a catalog/reference concept — the twin, on the sell side, of a
 * provider buying condition — resolved most-specific-first for a given
 * (customer, item, place) context by reading its {@see PricingConditionSelector}.
 * It carries at most one of three mutually exclusive effects :
 * - a list of stacked `adjustment` ({@see Adjustment}) applied in order (each a
 *   signed percentage or amount — a discount, or a surcharge when negative), or
 * - a `substitutesSegment` ({@see PriceSegmentation}) that swaps the buyer's
 *   usual tariff segment for another, or
 * - a `fixedPrice` ({@see MonetaryAmount}) that imposes a fixed net price.
 * A `free` flag may additionally mark the item as granted free of charge.
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
     * A list of stacked adjustments applied in order (e.g. −10 %, then −5 % on
     * the remaining amount). Each is a signed percentage or amount — a discount,
     * or a surcharge when negative. Always a list, even for a single adjustment.
     * Mutually exclusive with `substitutesSegment` and `fixedPrice`.
     * @var Adjustment|array|null
     */
    #[HydrateWith(Adjustment::class)]
    public null|array|Adjustment $adjustment ;

    /**
     * The resolved price category the condition targets, when `selector.itemScope` is `CATEGORY`.
     * Display-only : the resolver reads `selector.itemId`, never this.
     * @var DefinedTerm|array|null
     */
    #[HydrateAs(DefinedTerm::class)]
    public null|array|DefinedTerm $category = null ;

    /**
     * The resolved buyer the condition targets, when `selector.customerScope` is
     * `INDIVIDUAL`. Display-only : the resolver reads `selector.customerId`, never this.
     * @var Customer|array|null
     */
    #[HydrateAs(Customer::class)]
    public null|array|Customer $customer = null ;

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
     * A fixed net price imposed by this condition, applied *instead of* any
     * adjustment or segment substitution. Mutually exclusive with `adjustment`
     * and `substitutesSegment`.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $fixedPrice ;

    /**
     * Whether the item is granted free of charge under this condition.
     * @var bool|null
     */
    public ?bool $free ;

    /**
     * The resolved product the condition targets, when `selector.itemScope` is
     * `PRODUCT`. Display-only : the resolver reads `selector.itemId`, never this.
     * @var Product|array|null
     */
    #[HydrateAs(Product::class)]
    public null|array|Product $product = null ;

    /**
     * The resolved supplier the condition is constrained to, when `selector.providerId`
     * is set. Display-only : the resolver reads `selector.providerId`, never this.
     * @var Provider|array|null
     */
    #[HydrateAs(Provider::class)]
    public null|array|Provider $provider = null ;

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
     * The resolved company the condition is valid at, when `selector.areaScope` is
     * `COMPANY`. Display-only : the resolver reads `selector.areaServed`, never this.
     * @var Subsidiary|array|null
     */
    #[HydrateAs(Subsidiary::class)]
    public null|array|Subsidiary $subsidiary = null ;

    /**
     * The tariff segment substituted for the buyer's usual one, applied
     * *instead of* a discount. Mutually exclusive with `adjustment` and
     * `fixedPrice`.
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

    /**
     * The resolved point of sale the condition is valid at, when `selector.areaScope`
     * is `WAREHOUSE`. Display-only : the resolver reads `selector.areaServed`, never this.
     * @var Warehouse|array|null
     */
    #[HydrateAs(Warehouse::class)]
    public null|array|Warehouse $warehouse = null ;
}
