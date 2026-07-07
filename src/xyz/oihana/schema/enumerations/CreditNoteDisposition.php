<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * What becomes of the amount of a {@see \xyz\oihana\schema\business\documents\CreditNote} :
 * refunded in cash, reapplied against another invoice, or still pending.
 *
 * Captures the distinction Odoo's credit-note wizard makes explicit (a cash
 * refund versus a credit reapplied to a future invoice), which a bare credit
 * note otherwise leaves implicit.
 *
 * The value is free : a consumer may use one of the constants below, its own
 * free-text label, or a subclass adding project-specific dispositions.
 *
 * | Constant  | Description                                                      | Value                                                       |
 * |-----------|------------------------------------------------------------------|-------------------------------------------------------------|
 * | PENDING   | The credit has not been used yet.                                | https://schema.oihana.xyz/CreditNoteDisposition#Pending     |
 * | REAPPLIED | The credit was applied against another invoice.                  | https://schema.oihana.xyz/CreditNoteDisposition#Reapplied   |
 * | REFUNDED  | The credit was paid back to the customer in cash.                | https://schema.oihana.xyz/CreditNoteDisposition#Refunded    |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.3.0
 */
class CreditNoteDisposition extends Enumeration
{
    /**
     * The credit has not been used yet.
     */
    public const string PENDING = 'https://schema.oihana.xyz/CreditNoteDisposition#Pending' ;

    /**
     * The credit was applied against another invoice.
     */
    public const string REAPPLIED = 'https://schema.oihana.xyz/CreditNoteDisposition#Reapplied' ;

    /**
     * The credit was paid back to the customer in cash.
     */
    public const string REFUNDED = 'https://schema.oihana.xyz/CreditNoteDisposition#Refunded' ;
}
