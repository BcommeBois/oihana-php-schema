<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

use org\schema\Product;
use org\schema\QuantitativeValue;
use org\schema\Service;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\DeliveryLineTrait;

/**
 * A single line of a {@see DeliveryNote} : how much of a given
 * {@see BusinessDocumentLine} (identified by `position`) was actually
 * delivered, as opposed to ordered or left in backorder.
 *
 * This is the gap confirmed across every reference checked for this
 * namespace (UBL's `DespatchLine`, Odoo's `stock.move`, SAP's delivery
 * item) : a bare `orderDelivery` (a single {@see \org\schema\ParcelDelivery})
 * can say a parcel was shipped, but not how much of what was ordered it
 * actually contains — a blind spot the moment a delivery is only partial.
 *
 * `batchNumber`/`serialNumbers` cover the traceability a line may need
 * (food, pharma, warranty, product recalls) — both optional, since most
 * goods need neither.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class DeliveryLine extends StructuredValue
{
    use DeliveryLineTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The quantity of this line still in backorder (ordered but not yet delivered).
     * @var int|float|QuantitativeValue|array|null
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue $backorderQuantity ;

    /**
     * Why the backorder quantity could not be delivered (e.g. "out of stock").
     * @var string|null
     */
    public ?string $backorderReason ;

    /**
     * The batch/lot number of the delivered goods, when traceability applies.
     * @var string|null
     */
    public ?string $batchNumber ;

    /**
     * The quantity actually delivered on this line.
     * @var int|float|QuantitativeValue|array|null
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue $deliveredQuantity ;

    /**
     * The product or service this line concerns.
     * @var null|array|Product|Service
     */
    public null|array|Product|Service $item ;

    /**
     * The quantity originally ordered on this line.
     * @var int|float|QuantitativeValue|array|null
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue $orderedQuantity ;

    /**
     * The position of the originating {@see BusinessDocumentLine} this
     * delivery line reconciles against (e.g. the purchase order's line 1, 2, 3...).
     * @var int|string|null
     */
    public null|int|string $position ;

    /**
     * The serial numbers of the delivered items, when the goods are serialized.
     * @var null|array|string
     */
    public null|array|string $serialNumbers ;
}
