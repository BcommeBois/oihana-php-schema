<?php

namespace xyz\oihana\schema\products;

use org\schema\Thing;
use xyz\oihana\schema\constants\Oihana;

/**
 * The product's warehouse availability.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class ProductWarehouseAvailability extends Thing
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
}