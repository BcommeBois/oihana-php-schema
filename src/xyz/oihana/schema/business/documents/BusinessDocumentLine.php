<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\CompoundPriceSpecification;
use org\schema\DefinedTerm;
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
     * Why the goods on this line leave without being invoiced — a gift, a
     * breakage, a sample, goods that were the customer's to begin with.
     *
     * 🔑 **Its presence is what says the line is offered.** There is deliberately
     * no boolean beside it : an ERP carrying both a flag and a reason has been
     * observed with the two drifting apart, and a line claiming to be a gift
     * while still charging money is the one thing this property exists to prevent.
     *
     * The term is frozen by its code and its label (`id` + `name`), never by its
     * storage key : a controlled vocabulary re-harvested into a fresh collection
     * is renumbered, and a line pointing at a key would silently designate
     * another term.
     *
     * @var DefinedTerm|array|null
     * @since 1.4.0
     */
    #[HydrateAs(DefinedTerm::class)]
    public null|array|DefinedTerm $freeReason = null ;

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
     * A note meant for whoever prepares the goods, never for the customer.
     *
     * The sibling of the inherited `description`, which is what the customer
     * reads on the document : « reprendre les 3 colis palette 12, ne pas remettre
     * en stock » belongs on the picking slip and nowhere else. Keeping the two
     * apart is what lets a document be printed twice, for two audiences, from a
     * single line.
     *
     * @var string|null
     * @since 1.4.0
     */
    public ?string $technicalNote = null ;

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
