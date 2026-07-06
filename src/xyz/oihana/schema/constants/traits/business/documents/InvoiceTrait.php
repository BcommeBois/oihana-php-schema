<?php

namespace xyz\oihana\schema\constants\traits\business\documents;

/**
 * The property name constants of the {@see \xyz\oihana\schema\business\documents\Invoice} class.
 *
 * @package xyz\oihana\schema\constants\traits\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
trait InvoiceTrait
{
    const string ACCOUNT_ID             = 'accountId' ;
    const string BILLING_PERIOD         = 'billingPeriod' ;
    const string BROKER                 = 'broker' ;
    const string CATEGORY               = 'category' ;
    const string CONFIRMATION_NUMBER    = 'confirmationNumber' ;
    const string PAYMENT_DUE_DATE       = 'paymentDueDate' ;
    const string PAYMENT_STATUS         = 'paymentStatus' ;
    const string PROVIDER               = 'provider' ;
    const string REFERENCES_ORDER       = 'referencesOrder' ;
    const string SCHEDULED_PAYMENT_DATE = 'scheduledPaymentDate' ;
}
