<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

use org\schema\CategoryCode;
use org\schema\MonetaryAmount;
use org\schema\StructuredValue;
use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\EcoFeeRuleTrait;

/**
 * The calculation rule behind an environmental contribution (eco-fee) — e.g.
 * "0.25 EUR per unit for the small-electronics category, from 2026-01-01".
 *
 * A rule is a catalog/reference concept : it does not carry a monetary
 * effect by itself. Applying it on a document line is recorded by an
 * {@see AppliedEcoFee} (which references this rule and the resulting
 * amount) ; the actual monetary impact on totals always flows through an
 * {@see Adjustment} of type `environmentalFee`.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class EcoFeeRule extends StructuredValue
{
    use EcoFeeRuleTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The product category this rule applies to.
     * @var null|string|CategoryCode|Thing
     */
    public null|string|CategoryCode|Thing $category ;

    /**
     * The fee amount charged per unit.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $rate ;

    /**
     * The date from which the rule applies.
     * @var string|int|null
     */
    public null|string|int $validFrom ;

    /**
     * The date after which the rule no longer applies.
     * @var string|int|null
     */
    public null|string|int $validThrough ;
}
