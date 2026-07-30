<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\Product;
use org\schema\QuantitativeValue;
use org\schema\Service;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\GoodsReceiptLineTrait;
use xyz\oihana\schema\products\Product as OihanaProduct;

/**
 * A single line of a {@see GoodsReceiptConfirmation} : how much of a given
 * {@see DeliveryLine} (by `position`) the buyer actually received, versus what
 * the delivery announced — the buyer-side counterpart of {@see DeliveryLine}.
 *
 * `condition` records the state of the received goods (e.g. "good",
 * "damaged"), and `discrepancyNote` any mismatch worth flagging back to the
 * seller — the basis for a later {@see CreditNote} or {@see DebitNote}.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class GoodsReceiptLine extends StructuredValue
{
    use GoodsReceiptLineTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The state of the received goods (e.g. "good", "damaged", "expired").
     * @var string|null
     */
    public ?string $condition ;

    /**
     * Any mismatch between what was expected and what was received.
     * @var string|null
     */
    public ?string $discrepancyNote ;

    /**
     * The quantity the delivery announced for this line.
     * @var int|float|QuantitativeValue|array|null
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue $expectedQuantity ;

    /**
     * The product or service this line concerns.
     * The `Product|Service` union is resolved from the payload's `@type` : a raw
     * item hydrates into a {@see Service} when it says so, and into the
     * commerce-enriched {@see OihanaProduct} (a `org\schema\Product`) otherwise.
     *
     * @var null|array|Product|Service
     */
    #[HydrateWith(OihanaProduct::class, Service::class)]
    public null|array|Product|Service $item ;

    /**
     * The position of the originating {@see DeliveryLine} (or document line)
     * this receipt line reconciles against.
     * @var int|string|null
     */
    public null|int|string $position ;

    /**
     * The quantity actually received on this line.
     * @var int|float|QuantitativeValue|array|null
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue $receivedQuantity ;
}
