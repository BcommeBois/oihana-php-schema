<?php

namespace xyz\oihana\schema\products;

use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\PricingItemScope;
use xyz\oihana\schema\enumerations\PricingTargetScope;

/**
 * The selection criteria of a {@see PricingCondition} — *who* it targets, *what*
 * item it applies to and *where* it is valid.
 *
 * The selector is what the resolver reads to pick the most-specific condition
 * for a given (customer, item, place) context : the buyer axis
 * (`customerScope` + `customerId`) is resolved from the most precise
 * (`INDIVIDUAL`) down to the catch-all (`ALL`), and the item axis
 * (`itemScope` + `itemId`, refined by `categoryLevel` for hierarchical
 * categories) the same way. `areaServed` narrows the condition to one place
 * (a point of sale, a region…) or is left null to apply everywhere.
 *
 * @package xyz\oihana\schema\products
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
class PricingConditionSelector extends StructuredValue
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The place (point of sale, region…) the condition is restricted to,
     * or null to apply everywhere.
     * @var string|null
     */
    public ?string $areaServed ;

    /**
     * The category depth targeted when `itemScope` is `CATEGORY` and categories
     * are hierarchical (1 = top level). Null when not category-scoped.
     * @var int|null
     */
    public ?int $categoryLevel ;

    /**
     * The identifier of the targeted buyer (the customer, group or company id),
     * or null when `customerScope` is `ALL`.
     * @var string|null
     */
    public ?string $customerId ;

    /**
     * The granularity of the targeted buyer.
     * @var null|string|PricingTargetScope
     */
    public null|string|PricingTargetScope $customerScope ;

    /**
     * The identifier of the targeted item (the product, category or provider id),
     * or null when `itemScope` is `ALL`.
     * @var string|null
     */
    public ?string $itemId ;

    /**
     * The granularity of the targeted item.
     * @var null|string|PricingItemScope
     */
    public null|string|PricingItemScope $itemScope ;
}
