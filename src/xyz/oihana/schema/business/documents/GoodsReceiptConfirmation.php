<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\constants\traits\business\documents\GoodsReceiptConfirmationTrait;

/**
 * A goods-receipt confirmation — the buyer confirms having received the goods
 * of a {@see DeliveryNote}, reporting per-line the quantity actually received
 * and any discrepancy or damage.
 *
 * This is what UBL/Peppol's `ReceiptAdvice` actually models (the buyer-side
 * mirror of a despatch advice), NOT to be confused with this namespace's
 * {@see Receipt}, which is a proof of *payment*. It is the first buyer-side
 * document of an otherwise seller-centric hierarchy : where {@see DeliveryNote}
 * states what the seller shipped, this states what the buyer received.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class GoodsReceiptConfirmation extends BusinessDocument
{
    use GoodsReceiptConfirmationTrait ;

    /**
     * The per-line receipt detail : expected vs. received quantity, condition
     * and any discrepancy.
     * @var null|array|GoodsReceiptLine
     */
    #[HydrateWith(GoodsReceiptLine::class)]
    public null|array|GoodsReceiptLine $lines ;

    /**
     * The delivery note(s) this confirmation acknowledges receipt of.
     * @var null|array|DeliveryNote
     */
    #[HydrateWith(DeliveryNote::class)]
    public null|array|DeliveryNote $referencesDeliveryNote ;
}
