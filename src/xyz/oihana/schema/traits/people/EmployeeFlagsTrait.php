<?php

namespace xyz\oihana\schema\traits\people ;

use org\schema\traits\helpers\GetAdditionalPropertyTrait;

use xyz\oihana\schema\constants\PersonAdditionalProperty;

/**
 * Answers, on a person, which documents they are meant to receive.
 *
 * A customer's staff carries these answers as additional properties — one of them
 * is the person the quotes go to, another receives everything. Asking the question
 * out loud beats reading a list of properties at every call site.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\traits\people
 * @since   1.4.0
 */
trait EmployeeFlagsTrait
{
    use GetAdditionalPropertyTrait ;

    /**
     * Whether this person receives the delivery notes.
     */
    public function isDeliveryNoteRecipient() :bool
    {
        return $this->hasAdditionalPropertyFlag( PersonAdditionalProperty::IS_DELIVERY_NOTE_RECIPIENT ) ;
    }

    /**
     * Whether this person receives **every** document — the catch-all recipient.
     */
    public function isDocumentRecipient() :bool
    {
        return $this->hasAdditionalPropertyFlag( PersonAdditionalProperty::IS_DOCUMENT_RECIPIENT ) ;
    }

    /**
     * Whether this person receives the invoices.
     */
    public function isInvoiceRecipient() :bool
    {
        return $this->hasAdditionalPropertyFlag( PersonAdditionalProperty::IS_INVOICE_RECIPIENT ) ;
    }

    /**
     * Whether this person receives the purchase orders.
     */
    public function isOrderRecipient() :bool
    {
        return $this->hasAdditionalPropertyFlag( PersonAdditionalProperty::IS_ORDER_RECIPIENT ) ;
    }

    /**
     * Whether this person receives the quotes.
     */
    public function isQuoteRecipient() :bool
    {
        return $this->hasAdditionalPropertyFlag( PersonAdditionalProperty::IS_QUOTE_RECIPIENT ) ;
    }

    /**
     * Whether the applications are shown to this person.
     */
    public function showsApplications() :bool
    {
        return $this->hasAdditionalPropertyFlag( PersonAdditionalProperty::SHOW_APPLICATIONS ) ;
    }
}