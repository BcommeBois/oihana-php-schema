<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\AppliedEcoFeeTrait;

/**
 * The record of an {@see EcoFeeRule} applied on a {@see BusinessDocumentLine},
 * with the resulting amount — the audit trail answering *which rule, at what
 * quantity, produced this fee*.
 *
 * This class only documents the calculation ; the monetary effect on the
 * line/document totals is carried by a companion {@see Adjustment} of type
 * `environmentalFee` on the same line.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class AppliedEcoFee extends StructuredValue
{
    use AppliedEcoFeeTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The computed fee amount (the rule's rate × quantity).
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $amount ;

    /**
     * The quantity the rule's rate was applied to.
     * @var int|float|null
     */
    public null|int|float $quantity ;

    /**
     * The rule that was applied — a resolved reference, never a copy.
     * @var null|string|array|EcoFeeRule
     */
    public null|string|array|EcoFeeRule $rule ;
}
