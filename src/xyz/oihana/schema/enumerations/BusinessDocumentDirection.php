<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * The commercial direction of a business document, from the point of view of
 * the organization operating it.
 *
 * Orthogonal to the document's type (quote, order, invoice...) and to its
 * {@see BusinessDocumentStatus} lifecycle: it only tells which party — the
 * {@see BusinessDocument::$seller} or the
 * {@see BusinessDocument::$customer} — is
 * the operator's own organization.
 *
 * | Constant | Description                                                      | Value                                                        |
 * |----------|------------------------------------------------------------------|--------------------------------------------------------------|
 * | SALE     | The operator is the seller (an outbound, sales document).        | https://schema.oihana.xyz/BusinessDocumentDirection#Sale     |
 * | PURCHASE | The operator is the customer (an inbound, procurement document). | https://schema.oihana.xyz/BusinessDocumentDirection#Purchase |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.4.0
 */
class BusinessDocumentDirection extends Enumeration
{
    /**
     * The operator is the seller — an outbound, sales document.
     */
    public const string SALE = 'https://schema.oihana.xyz/BusinessDocumentDirection#Sale' ;

    /**
     * The operator is the customer — an inbound, procurement document.
     */
    public const string PURCHASE = 'https://schema.oihana.xyz/BusinessDocumentDirection#Purchase' ;
}