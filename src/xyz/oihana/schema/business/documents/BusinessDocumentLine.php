<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\MonetaryAmount;
use org\schema\PriceSpecification;
use org\schema\Product;
use org\schema\QuantitativeValue;
use org\schema\Service;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\BusinessDocumentLineTrait;
use xyz\oihana\schema\enumerations\UnitOfSaleType;

/**
 * A single line of a {@see BusinessDocument} : the item sold, its quantity
 * and price, the taxes and adjustments applying to it, and the resulting
 * line totals.
 *
 * `taxes` and `adjustments` are scoped to this line — a document can mix
 * lines taxed at different rates, or carry a line-specific discount,
 * independently of the document-level {@see DocumentTotals}.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class BusinessDocumentLine extends StructuredValue
{
    use BusinessDocumentLineTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The adjustments (discounts, surcharges, fees...) applying to this line.
     * @var null|array|Adjustment
     */
    #[HydrateWith(Adjustment::class)]
    public null|array|Adjustment $adjustments ;

    /**
     * The product or service sold on this line.
     * @var null|array|Product|Service
     */
    public null|array|Product|Service $item ;

    /**
     * The position of this line within the document (e.g. 1, 2, 3...).
     * @var int|string|null
     */
    public null|int|string $position ;

    /**
     * The unit price of the item.
     * @var MonetaryAmount|PriceSpecification|array|null
     */
    public null|array|MonetaryAmount|PriceSpecification $price ;

    /**
     * The quantity of the item sold on this line.
     * @var int|float|QuantitativeValue|array|null
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue $quantity ;

    /**
     * The line total before tax (quantity × price, adjustments applied).
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $subtotal ;

    /**
     * The taxes applying to this line.
     * @var null|array|TaxDetail
     */
    #[HydrateWith(TaxDetail::class)]
    public null|array|TaxDetail $taxes ;

    /**
     * The line total including tax.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $total ;

    /**
     * The unit of sale the quantity is expressed in.
     * @var null|string|UnitOfSaleType
     */
    public null|string|UnitOfSaleType $unit ;
}
