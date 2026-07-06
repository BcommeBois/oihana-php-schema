<?php

namespace xyz\oihana\schema\business\documents;

use xyz\oihana\schema\constants\traits\business\documents\QuoteTrait;

/**
 * A quote (estimate) offered to a customer ahead of an order.
 *
 * Not to be confused with {@see \org\schema\creativeWork\Quotation}, which
 * is a literary citation — an unrelated, pre-existing schema.org mirror
 * type sharing only the English word "quote".
 *
 * Reuses Schema.org's `validThrough` (already used by
 * {@see \org\schema\PriceSpecification}/{@see \org\schema\Offer} for "the
 * end of the validity of offer, price specification...") rather than
 * introducing a new `validUntil` name for the same concept.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class Quote extends BusinessDocument
{
    use QuoteTrait ;

    /**
     * The date after which the quote is no longer valid.
     * @var string|int|null
     */
    public null|string|int $validThrough ;
}
