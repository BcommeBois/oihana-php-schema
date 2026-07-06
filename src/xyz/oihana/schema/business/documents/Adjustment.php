<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

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
     * The kind of adjustment (discount, surcharge, shipping fee, environmental fee...).
     * @var null|string|PriceComponentType
     */
    public null|string|PriceComponentType $type ;
}
