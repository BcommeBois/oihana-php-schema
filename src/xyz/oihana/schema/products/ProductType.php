<?php

namespace xyz\oihana\schema\products;

use org\schema\DefinedTerm;
use xyz\oihana\schema\constants\Oihana;

/**
 * The product type entity.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class ProductType extends DefinedTerm
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Indicates if the product with the specific type can be stockable.
     * @var ?bool
     */
    public ?bool $stockable ;

    /**
     * Indicates if the product with the specific type can be trackable (statistically)
     * @var ?bool
     */
    public ?bool $trackable ;
}