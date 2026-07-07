<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * A structured reason a {@see \xyz\oihana\schema\business\documents\CreditNote}
 * was issued, alongside its free-text `reason` — so a consumer can process
 * credit notes by cause (a returned good, a pricing error…) rather than only
 * display a sentence to a human.
 *
 * Answers what UBL's `DiscrepancyResponse` and Peppol's mandatory reason
 * codes expect on a credit note beyond a free-text justification.
 *
 * The value is free : a consumer may use one of the constants below, its own
 * free-text label, or a subclass adding project-specific codes (the `OTHER`
 * member is provided for any reason not listed here).
 *
 * | Constant             | Description                                                | Value                                                             |
 * |----------------------|------------------------------------------------------------|-------------------------------------------------------------------|
 * | DUPLICATE_BILLING    | The original invoice was a duplicate.                      | https://schema.oihana.xyz/CreditNoteReasonCode#DuplicateBilling   |
 * | GOODS_RETURNED       | The customer returned the goods.                           | https://schema.oihana.xyz/CreditNoteReasonCode#GoodsReturned      |
 * | GOODWILL             | A goodwill gesture (commercial discount after the fact).   | https://schema.oihana.xyz/CreditNoteReasonCode#Goodwill           |
 * | OTHER                | Any reason not covered by the others.                      | https://schema.oihana.xyz/CreditNoteReasonCode#Other              |
 * | PRICING_ERROR        | The original invoice was over-priced.                      | https://schema.oihana.xyz/CreditNoteReasonCode#PricingError       |
 * | SERVICE_NOT_RENDERED | The billed service was not delivered.                      | https://schema.oihana.xyz/CreditNoteReasonCode#ServiceNotRendered |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.3.0
 */
class CreditNoteReasonCode extends Enumeration
{
    /**
     * The original invoice was a duplicate.
     */
    public const string DUPLICATE_BILLING = 'https://schema.oihana.xyz/CreditNoteReasonCode#DuplicateBilling' ;

    /**
     * The customer returned the goods.
     */
    public const string GOODS_RETURNED = 'https://schema.oihana.xyz/CreditNoteReasonCode#GoodsReturned' ;

    /**
     * A goodwill gesture (commercial discount after the fact).
     */
    public const string GOODWILL = 'https://schema.oihana.xyz/CreditNoteReasonCode#Goodwill' ;

    /**
     * Any reason not covered by the others.
     */
    public const string OTHER = 'https://schema.oihana.xyz/CreditNoteReasonCode#Other' ;

    /**
     * The original invoice was over-priced.
     */
    public const string PRICING_ERROR = 'https://schema.oihana.xyz/CreditNoteReasonCode#PricingError' ;

    /**
     * The billed service was not delivered.
     */
    public const string SERVICE_NOT_RENDERED = 'https://schema.oihana.xyz/CreditNoteReasonCode#ServiceNotRendered' ;
}
