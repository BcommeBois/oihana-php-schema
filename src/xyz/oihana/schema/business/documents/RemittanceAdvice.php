<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\MonetaryAmount;

use xyz\oihana\schema\constants\traits\business\documents\RemittanceAdviceTrait;

/**
 * A remittance advice — the document a *payer* sends to the payee to detail a
 * payment being (or about to be) made : which invoices it settles and the
 * total amount remitted.
 *
 * The payer-side counterpart of {@see Receipt} (which is the payee's proof
 * that a payment was received). The two deliberately coexist : they capture
 * the same event from opposite ends of the transaction — a buyer issues a
 * remittance advice, a seller issues a receipt — so a system acting for
 * either party can model its own side without overloading the other's type.
 * Defined in UBL as `RemittanceAdvice`.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class RemittanceAdvice extends BusinessDocument
{
    use RemittanceAdviceTrait ;

    /**
     * The total amount remitted by this advice.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $amountRemitted ;

    /**
     * The invoice(s) this remittance advice settles. A single payment may
     * cover more than one invoice.
     * @var null|array|Invoice
     */
    #[HydrateWith(Invoice::class)]
    public null|array|Invoice $referencesInvoice ;
}
