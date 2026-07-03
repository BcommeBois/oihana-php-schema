<?php

namespace xyz\oihana\schema\products;

use org\schema\Intangible;
use xyz\oihana\schema\constants\Oihana;

/**
 * Defines a set of quantity discounts by quantity applied by a provider on a specific product.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class PriceQuantityDiscount extends Intangible
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Represents the list of all next discounted prices by quantity.
     * @var null|string|array
     */
    public null|string|array $nextPrices ;

    /**
     * The list of all next quantities to apply a price discount.
     * @var null|string|array
     */
    public null|string|array $nextQuantities ;

    /**
     * Represents the list of all next percentage discounts applied by quantity.
     * @var null|string|array
     */
    public null|string|array $nextValues ;

    /**
     * Represents the list of all discounted prices by quantity.
     * @var null|string|array
     */
    public null|string|array $prices ;

    /**
     * The list of all quantities to apply a price discount.
     * @var null|string|array
     */
    public null|string|array $quantities ;

    /**
     * Represents the list of all percentage discounts applied by quantity.
     * @var null|string|array
     */
    public null|string|array $values ;
}
