<?php

namespace xyz\oihana\schema\constants\traits\business\documents;

/**
 * The property name constants of the {@see \xyz\oihana\schema\business\documents\BusinessDocument} class.
 *
 * @package xyz\oihana\schema\constants\traits\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
trait BusinessDocumentTrait
{
    const string ATTACHMENTS    = 'attachments' ;
    const string CURRENCY       = 'currency' ;
    const string CUSTOMER       = 'customer' ;
    const string DOCUMENT_LINES = 'documentLines' ;
    const string ISSUE_DATE     = 'issueDate' ;
    const string PAYMENT_TERMS  = 'paymentTerms' ;
    const string REFERENCES     = 'references' ;
    const string SELLER         = 'seller' ;
    const string STATUS         = 'status' ;
    const string TAXES          = 'taxes' ;
    const string TOTALS         = 'totals' ;
}
