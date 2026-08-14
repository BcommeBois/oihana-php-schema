<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\creativeWork\MediaObject;
use org\schema\Intangible;
use org\schema\Mass;
use org\schema\Organization;
use org\schema\ParcelDelivery;
use org\schema\Person;
use org\schema\Place;
use org\schema\PostalAddress;
use org\schema\QuantitativeValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\BusinessDocumentTrait;
use xyz\oihana\schema\enumerations\BusinessDocumentAuthority;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
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
     * The seller(s) the document is assigned to — the salesperson who carries
     * the deal, not the party issuing it.
     *
     * Distinct from {@see BusinessDocument::$seller}, the organization (or
     * person) the document is issued by : one seller issues every document of a
     * sales cycle, while the person answering for a given one is the pivot a
     * salesperson's own resources are scoped by (see
     * {@see \xyz\oihana\schema\helpers\pivots\sellerKey()}). Reuses the name,
     * the shape and the meaning {@see \xyz\oihana\schema\organizations\Customer::$assignedSeller}
     * already carries for the seller a customer is attached to — a document
     * commonly gets its own from there and then keeps it : a customer reassigned
     * later must not rewrite who took the order.
     *
     * Holds the raw seller reference as read from the source (a key, or the
     * joined row), or the resolved {@see Person}. No `#[HydrateAs]` is needed :
     * the union names a single class, so {@see \oihana\reflect\Reflection::hydrate()}
     * resolves a joined row — and each entry of a joined list — on its own,
     * while a bare key is left as read.
     *
     * @var string|int|array|Person|null
     * @since 1.4.0
     */
    public null|int|string|array|Person $assignedSeller ;

    /**
     * Files attached to the document (e.g. a signed PDF, supporting documents).
     * @var null|array|string|MediaObject
     */
    public null|array|string|MediaObject $attachments ;

    /**
     * The author of the document — the party (organization or person) who
     * authored it. Reuses the Schema.org `author` name.
     * @var null|Organization|Person|array
     */
    #[HydrateWith(Organization::class, Person::class)]
    public null|Organization|Person|array $author ;

    /**
     * Which system holds the truth about this document — ours, or the one it
     * was harvested from.
     *
     * 🔑 **Its absence means the document is ours.** Only what comes from
     * elsewhere states it, so nothing stored before this property existed
     * changes meaning.
     *
     * Answers a question no other property does : the class says what the
     * document is, `direction` says which side of the trade we stand on,
     * `status` says where it stands in its lifecycle — none of them says who
     * may change it. The distinction matters as soon as documents of both
     * origins share a collection : editing a mirrored one is a correction the
     * next refresh erases without a word.
     *
     * ⚠️ Carrying the fact is not enforcing it — refusing the write belongs to
     * whoever exposes the document.
     *
     * @var null|string|BusinessDocumentAuthority
     * @since 1.4.0
     */
    public null|string|BusinessDocumentAuthority $authority = null ;

    /**
     * The address the document is billed to — stored as a frozen copy so the
     * document stays self-contained even if the party's address book changes
     * later. Reuses the Schema.org `billingAddress` name from `Order`.
     * @var null|array|PostalAddress
     */
    #[HydrateAs(PostalAddress::class)]
    public null|array|PostalAddress $billingAddress ;

    /**
     * The individual to deal with about the document (the interlocutor on the
     * customer's side) — distinct from the {@see BusinessDocument::$customer}
     * party the document is addressed to. Absent from Schema.org's commercial
     * documents, hence added here.
     * @var null|array|Person
     */
    #[HydrateAs(Person::class)]
    public null|array|Person $contact ;

    /**
     * The currency the document's amounts are expressed in (ISO 4217, e.g. "EUR").
     * @var string|null
     */
    public ?string $currency ;

    /**
     * The party the document is addressed to.
     * @var null|array|Organization|Person
     */
    #[HydrateWith(Organization::class, Person::class)]
    public null|array|Organization|Person $customer ;

    /**
     * The commercial direction of the document (sale / purchase), from the
     * operator's point of view — which of {@see self::$seller} / {@see self::$customer}
     * is the operator's own organization.
     * @var null|string|BusinessDocumentDirection
     */
    public null|string|BusinessDocumentDirection $direction ;

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
     * The delivery attached to the document: the shipping address (a frozen
     * copy), the delivery method and the requested date. Reuses the name
     * already used by {@see DeliveryNote::$orderDelivery} for internal
     * consistency.
     * @var null|array|ParcelDelivery
     */
    #[HydrateAs(ParcelDelivery::class)]
    public null|array|ParcelDelivery $orderDelivery ;

    /**
     * The payment terms — free text, or a structured {@see PaymentSchedule}.
     * @var null|string|array|PaymentSchedule
     */
    #[HydrateAs(PaymentSchedule::class)]
    public null|string|array|PaymentSchedule $paymentTerms ;

    /**
     * The point of sale (store, outlet, warehouse) the document is bound to —
     * the outlet that carries it and drives its pricing. A frozen copy, like
     * the other header references.
     * @var null|array|Place
     */
    #[HydrateAs(Place::class)]
    public null|array|Place $pointOfSale ;

    /**
     * References to other related documents (e.g. a purchase order number quoted on an invoice).
     * @var null|array|string
     */
    public null|array|string $references ;

    /**
     * The party issuing the document.
     * @var null|array|Organization|Person
     */
    #[HydrateWith(Organization::class, Person::class)]
    public null|array|Organization|Person $seller ;

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

    /**
     * The space the goods the document covers take up.
     *
     * The twin of {@see BusinessDocument::$weight}, and read the same way : a
     * plain number when the unit is implicit, a {@see QuantitativeValue} when
     * it is stated (`{ value: 3.412, unitCode: "MTQ" }`) ; an array is hydrated
     * as the latter and sits there until it is.
     *
     * ⚠️ **Not part of {@see BusinessDocument::$totals}**, for the same reason
     * the weight is not : that class is the *monetary* summary and every one of
     * its properties is a {@see \org\schema\MonetaryAmount}.
     *
     * The header's side of {@see \xyz\oihana\schema\traits\HasPhysicalMeasures::$volume},
     * which the lines carry : usually the sum of theirs.
     *
     * @var null|array|int|float|QuantitativeValue
     * @since 1.4.0
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue $volume ;

    /**
     * What the goods the document covers weigh — the figure printed on a
     * delivery note or a quote, and the one a carrier is quoted from.
     *
     * Usually the sum, over the lines, of the quantity by the weight of the
     * unit it is expressed in. A plain number carries it when the unit is
     * implicit, a {@see QuantitativeValue} when the unit is stated
     * (`{ value: 326.5456, unitCode: "KGM" }`) ; an array is hydrated as the
     * latter. The union of {@see \org\schema\OfferShippingDetails::$weight}
     * widened with `array`, so a weight reads the same wherever it is met and
     * a raw row can still sit here until hydration replaces it — the mirror's
     * union predates that convention.
     *
     * ⚠️ **Not part of {@see BusinessDocument::$totals}** despite the pull of
     * the name : that class is the *monetary* summary and every one of its
     * properties is a {@see \org\schema\MonetaryAmount}. A mass sitting among
     * them would blur what it is.
     *
     * Deliberately neutral about gross and net. Should the distinction ever
     * be needed, it belongs to the `additionalType` of a `QuantitativeValue`,
     * never to a second property — two weights held in parallel eventually
     * disagree.
     *
     * The header's side of {@see \xyz\oihana\schema\traits\HasPhysicalMeasures::$weight},
     * which the lines carry. Declared here rather than taken from that trait :
     * the ⚠️ above is an argument about `totals`, a header-level property a
     * line does not have, and a trait absorbing it would either impose the
     * discussion on every line or lose it.
     *
     * @var null|array|int|float|QuantitativeValue|Mass
     * @since 1.4.0
     */
    #[HydrateAs(QuantitativeValue::class)]
    public null|array|int|float|QuantitativeValue|Mass $weight ;
}
