<?php

namespace xyz\oihana\schema\constants\traits\business\documents;

/**
 * The property name constants of the {@see \xyz\oihana\schema\business\documents\CreditNote} class.
 *
 * @package xyz\oihana\schema\constants\traits\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
trait CreditNoteTrait
{
    const string DISPOSITION        = 'disposition' ;
    const string REASON             = 'reason' ;
    const string REASON_CODE        = 'reasonCode' ;
    const string REFERENCES_INVOICE = 'referencesInvoice' ;
    const string REMAINING_BALANCE  = 'remainingBalance' ;
}
