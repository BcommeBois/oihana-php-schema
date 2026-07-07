<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateWith;

use org\schema\PaymentMethod;

use xyz\oihana\schema\constants\traits\business\documents\ReceiptTrait;

/**
 * A receipt (reçu) — proof that a payment was received.
 *
 * Reuses `org\schema\Invoice`'s `confirmationNumber` (literally "a number
 * that confirms the given order or payment has been received") and
 * `paymentMethod`/`paymentMethodId`. The received amount itself is not
 * duplicated here : it's already covered by the inherited
 * {@see BusinessDocument::$totals}. The date the payment was received is
 * the inherited {@see BusinessDocument::$issueDate} — a receipt is issued
 * at the moment payment is confirmed, so no separate `receivedDate` is needed.
 *
 * Two shapes are supported, and `referencesInvoice` is deliberately optional
 * to allow both :
 * - **Against one or more invoices** — the common case : `referencesInvoice`
 *   points at the {@see Invoice}(s) the payment settles (a single payment may
 *   clear several invoices at once).
 * - **A direct/cash sale, with no prior invoice** — the point-of-sale case
 *   (QuickBooks' `SalesReceipt`, Xero's `RECEIVE` bank transaction) : leave
 *   `referencesInvoice` null and carry the sale directly on the inherited
 *   {@see BusinessDocument::$documentLines}, {@see BusinessDocument::$taxes}
 *   and {@see BusinessDocument::$totals}, exactly as any other business
 *   document does — no separate "sales receipt" type is needed.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class Receipt extends BusinessDocument
{
    use ReceiptTrait ;

    /**
     * A number that confirms the given payment has been received.
     * @var string|int|null
     */
    public null|string|int $confirmationNumber ;

    /**
     * The method of payment used (e.g. credit card, bank transfer).
     * @var null|array|string|PaymentMethod
     */
    public null|array|string|PaymentMethod $paymentMethod ;

    /**
     * An identifier for the method of payment used (e.g. the last 4 digits of the credit card).
     * @var string|null
     */
    public ?string $paymentMethodId ;

    /**
     * The invoice(s) this receipt confirms payment for — optional. A single
     * payment may settle more than one invoice at once ; for a direct/cash
     * sale with no prior invoice, leave this null and carry the sale on the
     * inherited `documentLines`/`taxes`/`totals`.
     * @var null|array|Invoice
     */
    #[HydrateWith(Invoice::class)]
    public null|array|Invoice $referencesInvoice ;
}
