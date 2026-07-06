<?php

namespace xyz\oihana\schema\constants\traits\business\documents;

/**
 * The property name constants of the {@see \xyz\oihana\schema\business\documents\DocumentTotals} class.
 *
 * @package xyz\oihana\schema\constants\traits\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
trait DocumentTotalsTrait
{
    const string BALANCE_DUE    = 'balanceDue' ;
    const string PREPAID_AMOUNT = 'prepaidAmount' ;
    const string SUBTOTAL       = 'subtotal' ;
    const string TOTAL          = 'total' ;
    const string TOTAL_TAX      = 'totalTax' ;
}
