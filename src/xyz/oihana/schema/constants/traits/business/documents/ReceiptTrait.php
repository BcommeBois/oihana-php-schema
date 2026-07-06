<?php

namespace xyz\oihana\schema\constants\traits\business\documents;

/**
 * The property name constants of the {@see \xyz\oihana\schema\business\documents\Receipt} class.
 *
 * @package xyz\oihana\schema\constants\traits\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
trait ReceiptTrait
{
    const string CONFIRMATION_NUMBER = 'confirmationNumber' ;
    const string PAYMENT_METHOD      = 'paymentMethod' ;
    const string PAYMENT_METHOD_ID   = 'paymentMethodId' ;
    const string REFERENCES_INVOICE  = 'referencesInvoice' ;
}
