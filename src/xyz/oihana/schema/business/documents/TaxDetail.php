<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

use org\schema\CategoryCode;
use org\schema\MonetaryAmount;
use org\schema\StructuredValue;
use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\TaxDetailTrait;

/**
 * A single tax or environmental-contribution line applied on a
 * {@see BusinessDocumentLine} or on a whole {@see BusinessDocument}.
 *
 * Deliberately narrow : a `TaxDetail` carries one rate applied to one basis
 * (e.g. VAT at 20% on a 100 EUR basis). It never mixes VAT with
 * environmental contributions — those are tracked separately through
 * {@see AppliedEcoFee} and surfaced as an {@see Adjustment} of type
 * `environmentalFee` — so totals can always be broken down by tax
 * `category` without ambiguity.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class TaxDetail extends StructuredValue
{
    use TaxDetailTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The amount the tax rate is computed on (e.g. the line's subtotal).
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $basisAmount ;

    /**
     * The tax category (e.g. standard/reduced VAT rate).
     * @var null|string|CategoryCode|Thing
     */
    public null|string|CategoryCode|Thing $category ;

    /**
     * The tax rate, expressed as a percentage (e.g. 20 for 20%).
     * @var int|float|null
     */
    public null|int|float $rate ;

    /**
     * The computed tax amount (basisAmount × rate).
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $taxAmount ;
}
