<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;
use oihana\reflect\attributes\HydrateWith;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\AdjustmentTrait;
use xyz\oihana\schema\enumerations\PriceComponentType;

/**
 * A price adjustment applied on a {@see BusinessDocumentLine} or on a whole
 * {@see BusinessDocument} — inspired by UBL's `AllowanceCharge`.
 *
 * Covers discounts, surcharges, shipping fees, environmental fees, deposits
 * and packaging, all through the single `type` property (see
 * {@see PriceComponentType}) rather than one boolean/property per kind.
 * Environmental fees are always carried this way — never through a
 * dedicated "eco tax" property — with {@see EcoFeeRule} and
 * {@see AppliedEcoFee} documenting the rule that produced the amount.
 *
 * Exactly one of `amount` or `percentage` is expected to be set ; when both
 * are absent the adjustment carries no monetary effect (informational only).
 *
 * An adjustment with a monetary effect may carry the `taxes` it owes, at a
 * rate of its own : the tax on a shipping fee answers to the carrier, not to
 * the goods.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class Adjustment extends StructuredValue
{
    use AdjustmentTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The fixed monetary amount of the adjustment.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $amount ;

    /**
     * Whether this adjustment is already included in the base price it applies to,
     * rather than added on top of it.
     * @var bool|null
     */
    public ?bool $includedInBase ;

    /**
     * Whether this adjustment counts towards the document totals.
     *
     * 🔑 **Its absence means it counts.** Only an adjustment left out says so,
     * which keeps every document written before this property existed exactly
     * as true as it was.
     *
     * ⚠️ **Not to be confused with {@see Adjustment::$includedInBase}**, which
     * says something else entirely : that one tells whether the adjustment is
     * *already inside* the base price rather than added on top of it. This one
     * tells whether it reaches the totals at all.
     *
     * The case it answers is real and measured : a source may show a charge on
     * a document and deliberately leave it out of the amount due — a fee shown
     * for information, or one it refuses to commit to while an option is still
     * open. Summing it anyway overstates what is owed.
     *
     * 🔑 **The same name, the same default and the same meaning as
     * {@see BusinessDocumentLine::$includedInTotal}.** A flag that called
     * itself something else on the grounds that it lives on another class
     * would be a missed opportunity : a consumer learns the rule once.
     *
     * @var bool|null
     * @since 1.4.0
     */
    public ?bool $includedInTotal = null ;

    /**
     * The adjustment expressed as a percentage of the amount it applies to (e.g. 10 for 10%).
     * @var int|float|null
     */
    public null|int|float $percentage ;

    /**
     * A free-text explanation of the adjustment (e.g. "Loyalty discount").
     * @var string|null
     */
    public ?string $reason ;

    /**
     * The taxes owed on the adjustment itself.
     *
     * A charge is rarely tax-free : a shipping fee is taxed at its own rate,
     * which is not necessarily the rate of the goods it delivers. Stating it
     * here — rather than folding it into a document-level total — is what lets
     * a reader tell where each part of the tax came from, and keeps a document
     * whose lines and charges are taxed differently readable line by line.
     *
     * Same shape as {@see BusinessDocumentLine::$taxes} : one {@see TaxDetail}
     * per rate, each carrying the basis it applies to and the amount it
     * produces. An adjustment carrying no monetary effect carries no tax
     * either.
     *
     * @var null|array|TaxDetail
     */
    #[HydrateWith(TaxDetail::class)]
    public null|array|TaxDetail $taxes ;

    /**
     * The kind of adjustment (discount, surcharge, shipping fee, environmental fee...).
     * @var null|string|PriceComponentType
     */
    public null|string|PriceComponentType $type ;
}
