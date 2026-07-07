<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\constants\traits\business\documents\DebitNoteTrait;

/**
 * A debit note — the symmetric inverse of a {@see CreditNote} : it *increases*
 * what a customer owes, correcting an {@see Invoice} that was billed too low
 * (an under-charge, a forgotten line, an after-the-fact surcharge).
 *
 * UBL defines it as its own document type ; the maison layer follows suit for
 * symmetry with {@see CreditNote}. As with a credit note, the adjusting
 * amount flows through the inherited {@see BusinessDocument::$totals} — the
 * "this increases what's owed" meaning is carried by the document type itself,
 * not a sign convention. `reason` reuses the same free-text name/type as
 * {@see CreditNote::$reason} and {@see Adjustment::$reason}.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class DebitNote extends BusinessDocument
{
    use DebitNoteTrait ;

    /**
     * A free-text justification for the debit (e.g. "under-billed", "missing line").
     * @var string|null
     */
    public ?string $reason ;

    /**
     * The invoice(s) this debit note corrects. A single debit note may span
     * more than one invoice.
     * @var null|array|Invoice
     */
    #[HydrateWith(Invoice::class)]
    public null|array|Invoice $referencesInvoice ;
}
