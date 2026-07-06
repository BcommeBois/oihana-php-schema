<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\enumerations\StatusEnumeration;

/**
 * The lifecycle status of a business document (quote, purchase order, invoice...).
 *
 * Distinct from {@see \org\schema\enumerations\status\OrderStatus}, which tracks the
 * fulfillment of an order (shipped, delivered...), not the document's own lifecycle
 * (drafted, sent, accepted...).
 *
 * | Constant  | Description                                                          | Value                                                        |
 * |-----------|-----------------------------------------------------------------------|---------------------------------------------------------------|
 * | ACCEPTED  | The recipient accepted the document (e.g. a quote turned into an order). | https://schema.oihana.xyz/BusinessDocumentStatus#Accepted   |
 * | CANCELLED | The document was cancelled after being sent.                          | https://schema.oihana.xyz/BusinessDocumentStatus#Cancelled   |
 * | CONVERTED | The document was converted into another one (e.g. a quote into an order). | https://schema.oihana.xyz/BusinessDocumentStatus#Converted |
 * | DRAFT     | The document is still being prepared and has not been sent.           | https://schema.oihana.xyz/BusinessDocumentStatus#Draft       |
 * | EXPIRED   | The document's validity period has elapsed (e.g. a quote past its validity date). | https://schema.oihana.xyz/BusinessDocumentStatus#Expired |
 * | REJECTED  | The recipient rejected the document.                                   | https://schema.oihana.xyz/BusinessDocumentStatus#Rejected    |
 * | SENT      | The document has been sent to the recipient.                          | https://schema.oihana.xyz/BusinessDocumentStatus#Sent        |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.3.0
 */
class BusinessDocumentStatus extends StatusEnumeration
{
    /**
     * The recipient accepted the document (e.g. a quote turned into an order).
     */
    public const string ACCEPTED = 'https://schema.oihana.xyz/BusinessDocumentStatus#Accepted' ;

    /**
     * The document was cancelled after being sent.
     */
    public const string CANCELLED = 'https://schema.oihana.xyz/BusinessDocumentStatus#Cancelled' ;

    /**
     * The document was converted into another one (e.g. a quote into an order).
     */
    public const string CONVERTED = 'https://schema.oihana.xyz/BusinessDocumentStatus#Converted' ;

    /**
     * The document is still being prepared and has not been sent.
     */
    public const string DRAFT = 'https://schema.oihana.xyz/BusinessDocumentStatus#Draft' ;

    /**
     * The document's validity period has elapsed.
     */
    public const string EXPIRED = 'https://schema.oihana.xyz/BusinessDocumentStatus#Expired' ;

    /**
     * The recipient rejected the document.
     */
    public const string REJECTED = 'https://schema.oihana.xyz/BusinessDocumentStatus#Rejected' ;

    /**
     * The document has been sent to the recipient.
     */
    public const string SENT = 'https://schema.oihana.xyz/BusinessDocumentStatus#Sent' ;
}
