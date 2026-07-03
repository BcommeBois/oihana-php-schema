<?php

namespace xyz\oihana\schema\products;

use org\schema\Intangible;
use xyz\oihana\schema\constants\Oihana;

/**
 * The product's warehouse informations.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class ProductWarehouseInfo extends Intangible
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The availability of this item—for example In stock, Out of stock, Pre-order, etc.
     * @var string|object|null
     */
    public string|object|null $availability ;

    /**
     * Whether this provider is the preferred provider for the product in this warehouse.
     * @var null|bool
     */
    public ?bool $providerIsPreferred ;

    /**
     * The provider identifier.
     * @var null|int|string|object
     */
    public null|int|string|object $provider ;
}