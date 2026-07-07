<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\StatementEntryTrait;
use xyz\oihana\schema\enumerations\StatementEntryType;

/**
 * A single line of a {@see Statement} : a document that moved the account's
 * balance (a `Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `Receipt`…),
 * its amount, and the running balance after applying it.
 *
 * `document` is typed against the common {@see BusinessDocument} parent
 * rather than any single subtype, since a statement mixes several document
 * kinds over its period — or a plain string (e.g. a document number) when
 * the full object isn't available.
 *
 * The signed `amount` remains the single, functionally-sufficient movement
 * value ; `debitAmount`/`creditAmount` are an *optional* explicit split for
 * consumers that want UBL's / double-entry accounting's separate debit and
 * credit columns rather than one signed figure — they complement `amount`,
 * they don't replace it. `type` names the movement's nature explicitly (so
 * "invoices only" filtering needs no dereferencing of `document`), and
 * `dueDate` carries the maturity an aging breakdown is computed from.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class StatementEntry extends StructuredValue
{
    use StatementEntryTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The amount this entry moves the balance by.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $amount ;

    /**
     * The running balance of the account after this entry.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $balance ;

    /**
     * The credit part of this entry, when a consumer wants an explicit
     * debit/credit split rather than the single signed {@see $amount}.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $creditAmount ;

    /**
     * The date this entry occurred.
     * @var string|int|null
     */
    public null|string|int $date ;

    /**
     * The debit part of this entry, when a consumer wants an explicit
     * debit/credit split rather than the single signed {@see $amount}.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $debitAmount ;

    /**
     * The document this entry refers to.
     * @var null|array|string|BusinessDocument
     */
    #[HydrateAs(BusinessDocument::class)]
    public null|array|string|BusinessDocument $document ;

    /**
     * The maturity date this entry's amount is due — the date an aging
     * breakdown ({@see AgingSummary}) is computed from, distinct from the
     * {@see $date} the entry occurred.
     * @var string|int|null
     */
    public null|string|int $dueDate ;

    /**
     * The kind of movement this entry records. Reuses {@see StatementEntryType}
     * (invoice, payment, credit note, adjustment...) or a plain free-text label.
     * @var null|string|StatementEntryType
     */
    public null|string|StatementEntryType $type ;
}
