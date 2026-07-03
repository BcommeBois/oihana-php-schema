<?php

namespace xyz\oihana\schema\products;

use DateTime;

use org\schema\Intangible;

use xyz\oihana\schema\constants\Oihana;

/**
 * Represents product-related information provided by a specific supplier.
 * This schema is designed to describe provider data, including pricing, margins,
 * discounts, and purchase conditions for a given product.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class ProductProviderInfo extends Intangible
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The purchase price of the product.
     * @var int|float|null
     */
    public null|int|float $buyingPrice ;

    /**
     * Represents the margin (eg: 'marge' in french),
     * i.e. the discount or added value applied to the buying price of a product.
     * @var int|float|null|string|array
     */
    public null|int|float|string|array $buyingPriceMargin ;

    /**
     * The reference quantity value of the buying price component.
     * @var null|int|float
     */
    public null|int|float $buyingPriceReferenceQuantity ;

    /**
     * The unit of measure of the buying price.
     * @var string|null
     */
    public ?string $buyingPriceReferenceUnitCode ;

    /**
     * Indicates whether the provider offers apply variations and discounts for a specific product.
     */
    public ?bool $hasDiscounts ;

    /**
     * Indicates whether the provider offers a reduced price
     * for a certain purchase quantity of a product.
     */
    public ?bool $hasQuantityDiscount ;

    /**
     * The next buying price of the product.
     * @var int|float|null
     */
    public null|int|float $nextBuyingPrice ;

    /**
     * The next buying price date of the product.
     * @var null|int|string|DateTime
     */
    public null|int|string|DateTime $nextBuyingPriceDate ;

    /**
     * The price per unit of purchase,
     * @var int|float|null
     */
    public null|int|float $pricePerPurchaseUnit ;

    /**
     * Indicates if the provider is the primary of the product.
     * @var bool
     */
    public bool $primary = false ;

}