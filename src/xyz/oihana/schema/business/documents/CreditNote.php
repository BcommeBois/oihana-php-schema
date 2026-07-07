<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\constants\traits\business\documents\CreditNoteTrait;

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
 * second name for the same "free-text justification" concept.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class CreditNote extends BusinessDocument
{
    use CreditNoteTrait ;

    /**
     * A free-text justification for the credit (e.g. "goods returned", "billing error").
     * @var string|null
     */
    public ?string $reason ;

    /**
     * The invoice(s) this credit note corrects. A single credit note may
     * consolidate corrections spanning more than one invoice.
     * @var null|array|Invoice
     */
    #[HydrateWith(Invoice::class)]
    public null|array|Invoice $referencesInvoice ;
}
