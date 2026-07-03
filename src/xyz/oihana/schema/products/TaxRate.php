<?php

namespace xyz\oihana\schema\products;

use org\schema\UnitPriceSpecification;
use xyz\oihana\schema\constants\Oihana;

/**
 * The taxrate representation.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class TaxRate extends UnitPriceSpecification
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;
}