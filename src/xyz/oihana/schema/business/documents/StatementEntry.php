<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\StatementEntryTrait;

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
     * The date this entry occurred.
     * @var string|int|null
     */
    public null|string|int $date ;

    /**
     * The document this entry refers to.
     * @var null|array|string|BusinessDocument
     */
    #[HydrateAs(BusinessDocument::class)]
    public null|array|string|BusinessDocument $document ;
}
