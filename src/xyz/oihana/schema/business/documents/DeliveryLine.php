<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\Product;
use org\schema\QuantitativeValue;
use org\schema\Service;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\DeliveryLineTrait;
use xyz\oihana\schema\products\Product as OihanaProduct;
use xyz\oihana\schema\traits\HasPhysicalMeasures;

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
 * 🔑 **Everything this line measures describes what actually left**, never what
 * was ordered : `deliveredQuantity` against `orderedQuantity`, and the
 * `weight`/`volume` it takes from {@see HasPhysicalMeasures}. A line delivering
 * 84 of the 120 square meters ordered weighs the 84, which is why those figures
 * belong to the delivery line and not to the order's line — and why the lines
 * sum to {@see BusinessDocument::$weight} and {@see BusinessDocument::$volume}
 * on the note itself.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class DeliveryLine extends StructuredValue
{
    use DeliveryLineTrait     ,
        HasPhysicalMeasures   ;

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
     * The quantity originally ordered on this line.
     * @var int|float|QuantitativeValue|array|null
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue $orderedQuantity ;

    /**
     * The position of the originating {@see BusinessDocumentLine} this
     * delivery line reconciles against (e.g. the purchase order's line 1, 2, 3...).
     *
     * ⚠️ **Read relative to {@see DeliveryLine::$referencesOrder}.** As soon as a
     * note delivers more than one order, a position on its own names nothing —
     * line 325 of which order ?
     *
     * @var int|string|null
     */
    public null|int|string $position ;

    /**
     * The invoice that bills this line.
     *
     * 🔑 **The link lives here and not on the note.** A note can be billed by
     * more than one invoice — it delivers several orders, and each is invoiced
     * on its own — so an invoice reference on the header would have to choose
     * between them. At the line grain the question does not arise : a line
     * belongs to one order, hence to one invoice.
     *
     * The sibling of {@see DeliveryLine::$referencesOrder}, and it follows the
     * same rule : the union names a single class, so
     * {@see \oihana\reflect\Reflection::hydrate()} resolves a joined row on its
     * own, while a bare key is left as read.
     *
     * @var null|array|string|Invoice
     * @since 1.4.0
     */
    public null|array|string|Invoice $referencesInvoice ;

    /**
     * The purchase order this line comes from.
     *
     * A delivery line is a fact of its own, not a copy of the order's line and
     * not a pointer to it : it says what left **on this note**, of that line, on
     * that day. The quantity belongs to the delivery — which is why a line can
     * be delivered in halves, and why a document that merely pointed at the
     * order's lines could not express it.
     *
     * 🔑 **Without it, `orderedQuantity` cannot even be filled.** A source that
     * leaves the ordered quantity off the delivery only states it on the order,
     * and there is no way back to it from a position alone.
     *
     * Holds the raw order reference as read from the source, or the resolved
     * {@see PurchaseOrder}. No `#[HydrateAs]` is needed : the union names a
     * single class, so {@see \oihana\reflect\Reflection::hydrate()} resolves a
     * joined row on its own, while a bare key is left as read — and `array`
     * is what lets that row sit here until it is resolved.
     *
     * @var null|array|string|PurchaseOrder
     * @since 1.4.0
     */
    public null|array|string|PurchaseOrder $referencesOrder ;

    /**
     * The serial numbers of the delivered items, when the goods are serialized.
     * @var null|array|string
     */
    public null|array|string $serialNumbers ;
}
