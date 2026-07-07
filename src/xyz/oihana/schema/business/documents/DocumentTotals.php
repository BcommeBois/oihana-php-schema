<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateAs;

use org\schema\MonetaryAmount;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\DocumentTotalsTrait;

/**
 * The monetary summary of a {@see BusinessDocument} : total excluding tax,
 * total tax, total including tax, amount already prepaid and balance due,
 * plus the optional document-level allowance (discount) and charge totals.
 *
 * A dedicated value object rather than a reuse of
 * {@see \org\schema\CompoundPriceSpecification} : that schema.org type
 * bundles several {@see \org\schema\UnitPriceSpecification} that apply *in
 * parallel* for different dimensions of consumption (e.g. electricity +
 * cleaning fee) — a different concept from a document-level recap of a
 * single transaction's HT/tax/TTC/paid/due amounts.
 *
 * Every amount is a {@see MonetaryAmount}, the schema.org type recommended
 * for independent sums of money.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class DocumentTotals extends StructuredValue
{
    use DocumentTotalsTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The total of the document-level allowances (discounts) — the summed
     * effect of the discount {@see Adjustment} carried by the document,
     * mirroring UBL's `AllowanceTotalAmount`. A derived recap value ; the
     * individual allowances remain on {@see BusinessDocument::$adjustments}.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $allowanceTotal ;

    /**
     * The remaining amount to be paid (total − prepaidAmount).
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $balanceDue ;

    /**
     * The total of the document-level charges (surcharges, shipping fees,
     * packaging...) — the summed effect of the charge {@see Adjustment}
     * carried by the document, mirroring UBL's `ChargeTotalAmount`. A derived
     * recap value ; the individual charges remain on
     * {@see BusinessDocument::$adjustments}.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $chargeTotal ;

    /**
     * The amount already paid (e.g. a deposit or down payment).
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $prepaidAmount ;

    /**
     * The total amount excluding tax.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $subtotal ;

    /**
     * The total amount including tax (subtotal + totalTax).
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $total ;

    /**
     * The total tax amount.
     * @var MonetaryAmount|array|null
     */
    #[HydrateAs(MonetaryAmount::class)]
    public null|array|MonetaryAmount $totalTax ;
}
