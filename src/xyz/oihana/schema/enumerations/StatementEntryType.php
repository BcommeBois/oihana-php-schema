<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * The kind of movement a {@see \xyz\oihana\schema\business\documents\StatementEntry}
 * records on an account statement.
 *
 * Lets a statement line carry its nature explicitly, rather than leaving it
 * to be inferred from whichever document the entry references (or from the
 * sign of its amount) — the pattern Odoo follows with its `account.move`
 * `move_type`. Filtering "invoices only" or aggregating "payments only" then
 * needs no dereferencing of the underlying document.
 *
 * The value is free : a consumer may use one of the constants below, its own
 * free-text label, or a subclass adding project-specific types (the `OTHER`
 * member is provided for any movement not listed here).
 *
 * | Constant        | Description                                                    | Value                                                          |
 * |-----------------|----------------------------------------------------------------|----------------------------------------------------------------|
 * | ADJUSTMENT      | A manual adjustment or write-off on the account.               | https://schema.oihana.xyz/StatementEntryType#Adjustment        |
 * | CREDIT_NOTE     | A credit note reducing the balance.                            | https://schema.oihana.xyz/StatementEntryType#CreditNote        |
 * | INVOICE         | An invoice increasing the balance owed.                        | https://schema.oihana.xyz/StatementEntryType#Invoice           |
 * | OPENING_BALANCE | The balance brought forward at the start of the period.        | https://schema.oihana.xyz/StatementEntryType#OpeningBalance    |
 * | OTHER           | Any movement not covered by the others.                        | https://schema.oihana.xyz/StatementEntryType#Other             |
 * | PAYMENT         | A payment received, reducing the balance.                      | https://schema.oihana.xyz/StatementEntryType#Payment           |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.3.0
 */
class StatementEntryType extends Enumeration
{
    /**
     * A manual adjustment or write-off on the account.
     */
    public const string ADJUSTMENT = 'https://schema.oihana.xyz/StatementEntryType#Adjustment' ;

    /**
     * A credit note reducing the balance.
     */
    public const string CREDIT_NOTE = 'https://schema.oihana.xyz/StatementEntryType#CreditNote' ;

    /**
     * An invoice increasing the balance owed.
     */
    public const string INVOICE = 'https://schema.oihana.xyz/StatementEntryType#Invoice' ;

    /**
     * The balance brought forward at the start of the period.
     */
    public const string OPENING_BALANCE = 'https://schema.oihana.xyz/StatementEntryType#OpeningBalance' ;

    /**
     * Any movement not covered by the others.
     */
    public const string OTHER = 'https://schema.oihana.xyz/StatementEntryType#Other' ;

    /**
     * A payment received, reducing the balance.
     */
    public const string PAYMENT = 'https://schema.oihana.xyz/StatementEntryType#Payment' ;
}
