<?php

namespace xyz\oihana\schema\products;

use org\schema\QuantitativeValue;
use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a segmentation of price values derived from a quantitative value.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class PriceSegmentation extends QuantitativeValue
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Indicates if the price is the primary to calculates the others.
     * @var bool
     */
    public bool $primary ;
}