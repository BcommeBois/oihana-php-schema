<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\creativeWork\MediaObject;
use org\schema\Intangible;
use org\schema\Organization;
use org\schema\Person;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\BusinessDocumentTrait;
use xyz\oihana\schema\enumerations\BusinessDocumentStatus;

/**
 * The common parent of the quote → purchase order → invoice cycle (and its
 * neighbors: credit note, delivery note, receipt, statement).
 *
 * Intentionally extends {@see Intangible} rather than reusing the existing
 * schema.org mirror's {@see \org\schema\Order}/{@see \org\schema\Invoice} :
 * a business document qualifies a commercial transaction, it is not itself
 * an addressable resource, and this keeps the schema.org mirror untouched —
 * `org\schema\Order`/`org\schema\Invoice` are unaffected by this hierarchy
 * and existing consumers see no change. Property names are reused from
 * Schema.org wherever an equivalent concept exists (`customer`, `seller`) ;
 * new names (`documentLines`, `paymentTerms`, `taxes`, `totals`,
 * `attachments`, `references`, `issueDate`, `currency`, `status`) only cover
 * concepts absent from Schema.org for a generic commercial document.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class BusinessDocument extends Intangible
{
    use BusinessDocumentTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The document-level adjustments (discounts, surcharges, shipping fees,
     * packaging...) applying to the whole document, as opposed to a specific
     * line's {@see BusinessDocumentLine::$adjustments}.
     * @var null|array|Adjustment
     */
    #[HydrateWith(Adjustment::class)]
    public null|array|Adjustment $adjustments ;

    /**
     * Files attached to the document (e.g. a signed PDF, supporting documents).
     * @var null|array|string|MediaObject
     */
    public null|array|string|MediaObject $attachments ;

    /**
     * The currency the document's amounts are expressed in (ISO 4217, e.g. "EUR").
     * @var string|null
     */
    public ?string $currency ;

    /**
     * The party the document is addressed to.
     * @var Organization|Person|null
     */
    public null|Organization|Person $customer ;

    /**
     * The lines of the document.
     * @var null|array|BusinessDocumentLine
     */
    #[HydrateWith(BusinessDocumentLine::class)]
    public null|array|BusinessDocumentLine $documentLines ;

    /**
     * The date the document was issued.
     * @var string|int|null
     */
    public null|string|int $issueDate ;

    /**
     * The payment terms — free text, or a structured {@see PaymentSchedule}.
     * @var null|string|array|PaymentSchedule
     */
    #[HydrateAs(PaymentSchedule::class)]
    public null|string|array|PaymentSchedule $paymentTerms ;

    /**
     * References to other related documents (e.g. a purchase order number quoted on an invoice).
     * @var null|array|string
     */
    public null|array|string $references ;

    /**
     * The party issuing the document.
     * @var Organization|Person|null
     */
    public null|Organization|Person $seller ;

    /**
     * The lifecycle status of the document.
     * @var null|string|BusinessDocumentStatus
     */
    public null|string|BusinessDocumentStatus $status ;

    /**
     * The document-level taxes (as opposed to a specific line's taxes).
     * @var null|array|TaxDetail
     */
    #[HydrateWith(TaxDetail::class)]
    public null|array|TaxDetail $taxes ;

    /**
     * The document's monetary summary.
     * @var null|array|DocumentTotals
     */
    #[HydrateAs(DocumentTotals::class)]
    public null|array|DocumentTotals $totals ;
}
