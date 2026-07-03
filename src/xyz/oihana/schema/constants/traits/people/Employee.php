<?php

namespace xyz\oihana\schema\constants\traits\people ;

/**
 * Constants representing common custom and additional properties for an Employee.
 *
 * These constants are used to reference standardized property names
 * in the schema representation of a specific Person entity.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants\traits\people
 * @since   1.3.0
 */
trait Employee
{
    /**
     * Determines whether the contact is the delivery note recipient.
     */
    public const string IS_DELIVERY_NOTE_RECIPIENT = 'isDeliveryNoteRecipient' ;

    /**
     * Determines whether the contact is the document recipient.
     */
    public const string IS_DOCUMENT_RECIPIENT = 'isDocumentRecipient' ;

    /**
     * Determines whether the contact is the invoice recipient.
     */
    public const string IS_INVOICE_RECIPIENT = 'isInvoiceRecipient' ;

    /**
     * Determines whether the contact is the order recipient.
     */
    public const string IS_ORDER_RECIPIENT = 'isOrderRecipient' ;

    /**
     * Determines whether the contact is the quote recipient.
     */
    public const string IS_QUOTE_RECIPIENT = 'isQuoteRecipient' ;

    /**
     * Indicates if the customer's applications should be displayed.
     */
    public const string SHOW_APPLICATIONS = "showApplications" ;
}