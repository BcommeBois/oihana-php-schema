<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\Duration;
use org\schema\MonetaryAmount;

use xyz\oihana\schema\constants\traits\business\documents\StatementTrait;

/**
 * A statement (relevé de compte) — a periodic recap of the documents that
 * moved an account's balance (quotes turned into orders, invoices, credit
 * notes, receipts…), with an opening and a closing balance.
 *
 * The only class of this lot that isn't a thin, one-property subclass of
 * {@see BusinessDocument} : it introduces its own line concept,
 * {@see StatementEntry} (document reference + amount + running balance),
 * distinct from {@see BusinessDocumentLine} (which prices a product/service,
 * not an account movement).
 *
 * Reuses `org\schema\Invoice`'s `billingPeriod` name for the interval the
 * statement covers (same concept: "the time interval used to compute
 * the document"). `openingBalance`/`closingBalance` have no Schema.org
 * equivalent — UBL's `Statement` names them `BeginningBalanceAmount`/
 * `EndingBalanceAmount`, kept here as plain camelCase to match this
 * namespace's style. `totalDebit`/`totalCredit` are the period's aggregate
 * debit and credit totals (UBL's `TotalDebitAmount`/`TotalCreditAmount`),
 * and `agingSummary` the accounts-receivable aging breakdown a statement of
 * account is usually expected to expose.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class Statement extends BusinessDocument
{
    use StatementTrait ;

    /**
     * The accounts-receivable aging breakdown of the closing balance
     * (current, 1–30, 31–60, 61–90, over 90 days).
     * @var null|array|AgingSummary
     */
    #[HydrateAs(AgingSummary::class)]
    public null|array|AgingSummary $agingSummary ;

    /**
     * The time interval covered by the statement.
     * @var null|array|Duration|int|float
     */
    public null|array|Duration|int|float $billingPeriod ;

    /**
     * The account balance at the end of the period.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $closingBalance ;

    /**
     * The documents that moved the balance during the period.
     * @var null|array|StatementEntry
     */
    #[HydrateWith(StatementEntry::class)]
    public null|array|StatementEntry $entries ;

    /**
     * The account balance at the start of the period.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $openingBalance ;

    /**
     * The aggregate of all credit movements over the period.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $totalCredit ;

    /**
     * The aggregate of all debit movements over the period.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $totalDebit ;
}
