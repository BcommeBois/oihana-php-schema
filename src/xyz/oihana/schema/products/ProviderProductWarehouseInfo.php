<?php

namespace xyz\oihana\schema\products;

use org\schema\Thing;
use xyz\oihana\schema\constants\Oihana;

/**
 * The product warehouse informations.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class ProviderProductWarehouseInfo extends Thing
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The buying price of the product.
     * @var int|float|null
     */
    public null|int|float $buyingPrice ;

    /**
     * The next buying price of the product.
     * @var int|float|null
     */
    public null|int|float $nextBuyingPrice ;

    /**
     * The price per unit of purchase.
     * @var int|float|null
     */
    public null|int|float $pricePerPurchaseUnit ;
}