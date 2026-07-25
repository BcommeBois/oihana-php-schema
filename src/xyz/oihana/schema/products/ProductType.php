<?php

namespace xyz\oihana\schema\products;

use org\schema\DefinedTerm;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\traits\HasColor;

/**
 * The product type entity : the functional family of a product — how it is
 * stocked and tracked.
 *
 * It is a flat vocabulary (a plain {@see DefinedTerm}, with no SKOS hierarchy,
 * unlike the thesaurus categories) that also carries the house display
 * {@see HasColor::$color} — the very same `#RRGGBB` presentation hint the
 * thesaurus families expose, so a product type can be tinted in a user
 * interface like any other classification term.
 *
 * Its property name constants (`COLOR`, `STOCKABLE`, `TRACKABLE`) live in the
 * {@see \xyz\oihana\schema\constants\traits\products\ProductType} constants
 * trait, aggregated into {@see Oihana}.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\constants\Oihana;
 * use xyz\oihana\schema\products\ProductType;
 *
 * $type = new ProductType
 * ([
 *     'name'            => 'Bottled wine' ,
 *     'termCode'        => 'BOTTLE' ,
 *     Oihana::COLOR     => '#7B1E3A' ,
 *     Oihana::STOCKABLE => true ,
 *     Oihana::TRACKABLE => true ,
 * ]);
 * ```
 *
 * @see HasColor
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.3.0
 */
class ProductType extends DefinedTerm
{
    use HasColor ;

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