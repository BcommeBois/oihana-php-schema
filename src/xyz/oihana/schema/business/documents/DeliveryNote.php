<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\ParcelDelivery;

use xyz\oihana\schema\constants\traits\business\documents\DeliveryNoteTrait;

/**
 * A delivery note (bon de livraison) — attests the physical delivery of the
 * goods of a {@see PurchaseOrder}.
 *
 * Reuses {@see \org\schema\Order}'s own `orderDelivery` property name and its
 * {@see ParcelDelivery} type (address, tracking number/url, expected arrival
 * window, delivery method, shipped items…) rather than re-inventing shipment
 * tracking from scratch.
 *
 * `orderDelivery` alone only says a parcel was shipped, not how much of what
 * was ordered it actually contains. `lines` closes that gap — a list of
 * {@see DeliveryLine}, one per originating {@see BusinessDocumentLine}
 * (identified by `position`), each reconciling ordered vs. delivered vs.
 * backorder quantity. `proofOfDelivery` records who confirmed receipt, when,
 * and any discrepancy noted at that moment.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class DeliveryNote extends BusinessDocument
{
    use DeliveryNoteTrait ;

    /**
     * The per-line delivery detail : ordered vs. delivered vs. backorder
     * quantity, reconciled against the originating document's lines.
     * @var null|array|DeliveryLine
     */
    #[HydrateWith(DeliveryLine::class)]
    public null|array|DeliveryLine $lines ;

    /**
     * The delivery of the parcel related to this delivery note.
     * @var null|array|ParcelDelivery
     */
    #[HydrateAs(ParcelDelivery::class)]
    public null|array|ParcelDelivery $orderDelivery ;

    /**
     * The confirmation that the goods were received.
     * @var null|array|ProofOfDelivery
     */
    #[HydrateAs(ProofOfDelivery::class)]
    public null|array|ProofOfDelivery $proofOfDelivery ;
}
