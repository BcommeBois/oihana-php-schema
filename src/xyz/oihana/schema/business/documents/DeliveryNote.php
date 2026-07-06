<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

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
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class DeliveryNote extends BusinessDocument
{
    use DeliveryNoteTrait ;

    /**
     * The delivery of the parcel related to this delivery note.
     * @var null|array|ParcelDelivery
     */
    #[HydrateAs(ParcelDelivery::class)]
    public null|array|ParcelDelivery $orderDelivery ;
}
