<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\CompoundPriceSpecification;
use org\schema\MonetaryAmount;
use org\schema\PriceSpecification;
use org\schema\Product;
use org\schema\PropertyValue;
use org\schema\QuantitativeValue;
use org\schema\Service;
use org\schema\StructuredValue;
use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\BusinessDocumentLineTrait;
use xyz\oihana\schema\enumerations\UnitOfSaleType;
use xyz\oihana\schema\products\Product as OihanaProduct;
use xyz\oihana\schema\traits\HasColor;

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
    use BusinessDocumentLineTrait ,
        HasColor                  ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * A property-value pair representing an additional characteristic of the entity, e.g. a product feature or another characteristic for which there is no matching property in schema.org.
     * @var null|array|PropertyValue
     */
    #[HydrateWith( PropertyValue::class ) ]
    public null|array|PropertyValue $additionalProperty = null ;

    /**
     * The adjustments (discounts, surcharges, fees...) applying to this line.
     * @var null|array|Adjustment
     */
    #[HydrateWith(Adjustment::class)]
    public null|array|Adjustment $adjustments ;

    /**
     * The product or service sold on this line.
     *
     * The `Product|Service` union is resolved from the payload's `@type` : a raw
     * item hydrates into a {@see Service} when it says so, and into the
     * commerce-enriched {@see OihanaProduct} (a `org\schema\Product`) otherwise.
     *
     * @var null|array|Product|Service
     */
    #[HydrateWith(OihanaProduct::class, Service::class)]
    public null|array|Product|Service $item ;

    /**
     * The position of this line within the document (e.g. 1, 2, 3...).
     * @var int|string|null
     */
    public null|int|string $position ;

    /**
     * The unit price of the item.
     *
     * A raw payload is hydrated into a {@see CompoundPriceSpecification}, so a
     * unit price can be broken down into the {@see UnitPriceSpecification}
     * components applying in parallel (base price, eco-fee, deposit...).
     *
     * @var MonetaryAmount|PriceSpecification|array|null
     */
    #[HydrateAs(CompoundPriceSpecification::class)]
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
