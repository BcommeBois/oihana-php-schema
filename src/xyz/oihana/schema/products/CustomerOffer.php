<?php

namespace xyz\oihana\schema\products;

use oihana\reflect\attributes\HydrateAs;

use org\schema\OfferForPurchase;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\organizations\Customer;

/**
 * An offer to sell one product to one specific customer : the price of the
 * customer's tariff segment at their assigned warehouse, optionally reached
 * through a pricing condition.
 *
 * It specializes {@see OfferForPurchase} for a single, named buyer. The whole
 * inherited pricing surface is reused as-is : `price`, `priceCurrency`,
 * `priceSpecification` (typically a `CompoundPriceSpecification` whose
 * components decompose list price, discount and sale price), `eligibleCustomerType`
 * (the applied segment — the substituted one when a condition swaps it),
 * `availableAtOrFrom` (the customer's warehouse), `validFrom` / `validThrough`
 * and `seller`. Two own properties name the beneficiary and the rule that
 * produced the price :
 * - `customer` — a lightweight reference to the buyer this price is for ;
 * - `appliedCondition` — the {@see PricingCondition} that produced the price,
 *   or `null` when the customer's base tariff applies as-is.
 *
 * @package xyz\oihana\schema\products
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.4.0
 */
class CustomerOffer extends OfferForPurchase
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The pricing condition applied to reach this price ; `null` when the
     * customer's base tariff applies as-is.
     * @var PricingCondition|array|null
     */
    #[HydrateAs(PricingCondition::class)]
    public null|array|PricingCondition $appliedCondition ;

    /**
     * The customer this offer is priced for (a lightweight reference).
     * @var Customer|array|null
     */
    #[HydrateAs(Customer::class)]
    public null|array|Customer $customer ;
}
