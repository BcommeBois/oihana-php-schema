<?php

namespace xyz\oihana\schema\constants\traits\business\documents;

/**
 * The property name constants of the {@see \xyz\oihana\schema\business\documents\Statement} class.
 *
 * @package xyz\oihana\schema\constants\traits\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
trait StatementTrait
{
    const string AGING_SUMMARY   = 'agingSummary' ;
    const string BILLING_PERIOD  = 'billingPeriod' ;
    const string CLOSING_BALANCE = 'closingBalance' ;
    const string ENTRIES         = 'entries' ;
    const string OPENING_BALANCE = 'openingBalance' ;
    const string TOTAL_CREDIT    = 'totalCredit' ;
    const string TOTAL_DEBIT     = 'totalDebit' ;
}
