<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\MonetaryAmount;

use xyz\oihana\schema\constants\traits\business\documents\CreditNoteTrait;
use xyz\oihana\schema\enumerations\CreditNoteDisposition;
use xyz\oihana\schema\enumerations\CreditNoteReasonCode;

/**
 * A credit note (avoir) — corrects or cancels all or part of an {@see Invoice}
 * already issued.
 *
 * The corrected amount flows through the inherited {@see BusinessDocument::$totals}
 * (a positive recap of the credited amounts) — the fact that it reduces
 * what's owed is carried by the document type itself (a `CreditNote`), not
 * by a sign convention on `totals`.
 *
 * `reason` reuses the same name/type already established by
 * {@see Adjustment::$reason} in this namespace, rather than inventing a
 * second name for the same "free-text justification" concept ; `reasonCode`
 * adds the *structured* cause (UBL's `DiscrepancyResponse`, Peppol's reason
 * codes) alongside it, so a consumer can process credit notes by cause, not
 * only display a sentence. `remainingBalance` carries the not-yet-applied
 * part of the credit (Xero's `RemainingCredit`, QuickBooks' `Balance`), and
 * `disposition` whether that credit was refunded, reapplied or is still
 * pending (the distinction Odoo's reversal wizard makes explicit).
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class CreditNote extends BusinessDocument
{
    use CreditNoteTrait ;

    /**
     * What becomes of the credit : refunded, reapplied or still pending.
     * Reuses {@see CreditNoteDisposition} or a plain free-text label.
     * @var null|string|CreditNoteDisposition
     */
    public null|string|CreditNoteDisposition $disposition ;

    /**
     * A free-text justification for the credit (e.g. "goods returned", "billing error").
     * @var string|null
     */
    public ?string $reason ;

    /**
     * The structured cause of the credit, alongside the free-text {@see $reason}.
     * Reuses {@see CreditNoteReasonCode} or a plain free-text label.
     * @var null|string|CreditNoteReasonCode
     */
    public null|string|CreditNoteReasonCode $reasonCode ;

    /**
     * The invoice(s) this credit note corrects. A single credit note may
     * consolidate corrections spanning more than one invoice.
     * @var null|array|Invoice
     */
    #[HydrateWith(Invoice::class)]
    public null|array|Invoice $referencesInvoice ;

    /**
     * The part of the credit not yet applied to an invoice or refunded.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $remainingBalance ;
}
