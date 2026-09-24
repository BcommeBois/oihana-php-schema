<?php

namespace xyz\oihana\schema\statistics;

use oihana\reflect\attributes\HydrateAs;

use org\schema\DefinedTerm;
use org\schema\Person;
use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\statistics\CustomerReceivablesTrait;
use xyz\oihana\schema\constants\traits\statistics\CustomerStatisticsTrait;
use xyz\oihana\schema\constants\traits\statistics\HasReceivablesTrait;
use xyz\oihana\schema\organizations\Customer;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\traits\HasReceivables;

/**
 * What one customer owes and is late with, at one date.
 *
 * A {@see Statistics} record whose subject is a {@see Customer}, carrying the
 * figures of {@see HasReceivables} : the amount past due, split by how long it
 * has been late, the part of it in doubt, how late the oldest piece is and how
 * many pieces there are. Its {@see Statistics::$direction} is a sale — the
 * operator is the creditor.
 *
 * 🔑 **A snapshot, not a year.** Every other family describes a year of trade
 * cut month by month ; this one describes a debt as it stood on one day, named
 * by {@see CustomerReceivables::$observationDate}. It carries no
 * {@see Statistics::$year} and no {@see Statistics::$observationPeriod} — both
 * are left unset and never serialized —, and each of its measures holds a
 * `value` and never a `values`. A new reading replaces the previous one.
 *
 * 🔑 **The three dimensions are the ones the customer had when the record was
 * written**, copied from the customer rather than joined at read time, exactly
 * as {@see CustomerStatistics} does : who holds the account
 * ({@see CustomerReceivables::$assignedSeller}), which point of sale serves it
 * ({@see CustomerReceivables::$assignedPOS}) and what kind of customer it is
 * ({@see CustomerReceivables::$category}). They are what lets a reader group a
 * portfolio, a branch or a kind of customer — the group's own companies, for
 * one — without walking back to the customer for every figure.
 *
 * ⚠️ **And they are a photograph.** The record credits the debt to whoever holds
 * the account *now* — the one who will chase it —, not to whoever made the sale.
 * A customer reassigned takes its late payments along at the next reading.
 *
 * What it is not : a statement of account. A
 * {@see \xyz\oihana\schema\business\documents\Statement} lists the pieces that
 * moved an account over a period ; this record sums what is late, and names no
 * piece.
 *
 * The amounts are those of the books, tax included.
 *
 * @package xyz\oihana\schema\statistics
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class CustomerReceivables extends Statistics
{
    use CustomerReceivablesTrait ,
        CustomerStatisticsTrait  ,
        HasReceivables           ,
        HasReceivablesTrait      ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The customer this debt is owed by.
     *
     * Redeclares {@see Statistics::$about} with the same union, to name the
     * subject : a stored row is read back as a {@see Customer}, a bare code is
     * left as read.
     *
     * @var null|int|string|array|Thing
     * @since 1.5.0
     */
    #[HydrateAs(Customer::class)]
    public null|int|string|array|Thing $about ;

    /**
     * The point of sale the customer is served by.
     *
     * A code, or the resolved warehouse. Reuses the name, the shape and the
     * meaning {@see Customer::$assignedPOS} already carries.
     *
     * @var null|int|string|array|Warehouse
     * @since 1.5.0
     */
    public null|int|string|array|Warehouse $assignedPOS ;

    /**
     * The salesperson the customer is attached to — the one who chases the debt.
     *
     * A code, or the resolved person. Reuses the name, the shape and the meaning
     * {@see Customer::$assignedSeller} already carries.
     *
     * @var null|int|string|array|Person
     * @since 1.5.0
     */
    public null|int|string|array|Person $assignedSeller ;

    /**
     * The kind of customer this is.
     *
     * A code, or the resolved term. Reuses the name, the shape and the meaning
     * {@see \xyz\oihana\schema\organizations\Company::$category} already
     * carries. It is what lets a reader set aside the debts of a kind of
     * customer — the operator's own companies, typically, whose late payments
     * are not a risk of the same nature.
     *
     * @var null|string|array|DefinedTerm
     * @since 1.5.0
     */
    public null|string|array|DefinedTerm $category ;

    /**
     * The date the figures were read at, as an ISO 8601 date (`2026-09-22`).
     *
     * The figures are true on that day and on no other. Same term, same format
     * as {@see \org\schema\Observation::$observationDate}, which every measure
     * of this record could carry on its own ; stated once here instead, since a
     * snapshot reads all of them on the same day.
     *
     * Not to be confused with `modified`, which says when the record was written
     * down : the two usually coincide, and only this one says which day the books
     * were read on when they do not.
     *
     * @var null|string
     * @since 1.5.0
     */
    public null|string $observationDate ;
}
