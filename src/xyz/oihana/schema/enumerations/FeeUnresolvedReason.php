<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * Why a fee that is due could not be priced.
 *
 * A published rate does not always convert into the unit an item is billed in.
 * A rate stated per tonne needs a weight ; a rate stated per piece needs to
 * know how many pieces a package holds. When the catalogue is missing that
 * link, the fee is still **due** — it just cannot be quantified.
 *
 * 🔑 **Which is why the entry exists without a `price`**, carrying its `rate`
 * and one of these reasons : « environmental contribution to be determined »
 * reads as an answer, where a zero reads as « nothing is owed » and is simply
 * false. A consumer multiplying a quantity by an absent `price` gets zero or an
 * error — never a wrong amount.
 *
 * | Constant                | Description                                                                        | Value                                                                |
 * |-------------------------|------------------------------------------------------------------------------------|----------------------------------------------------------------------|
 * | MISSING_FEE_RATE        | No published rate is attached to the item at all.                                    | https://schema.oihana.xyz/FeeUnresolvedReason#MissingFeeRate          |
 * | MISSING_PRODUCT_MEASURE | The measure the rate is stated in — weight, volume, thickness — is absent or zero.   | https://schema.oihana.xyz/FeeUnresolvedReason#MissingProductMeasure   |
 * | UNKNOWN_PACKAGE_CONTENT | The item is billed by the package and how much that package holds is unknown.        | https://schema.oihana.xyz/FeeUnresolvedReason#UnknownPackageContent   |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.4.0
 */
class FeeUnresolvedReason extends Enumeration
{
    /**
     * No published rate is attached to the item at all — nothing to convert, and nothing to state either.
     */
    public const string MISSING_FEE_RATE = 'https://schema.oihana.xyz/FeeUnresolvedReason#MissingFeeRate' ;

    /**
     * The measure the rate is stated in — a weight, a volume, a thickness — is absent or zero on the item.
     */
    public const string MISSING_PRODUCT_MEASURE = 'https://schema.oihana.xyz/FeeUnresolvedReason#MissingProductMeasure' ;

    /**
     * The item is billed by the package, and how much that package holds is unknown — the rate cannot be brought down to it.
     */
    public const string UNKNOWN_PACKAGE_CONTENT = 'https://schema.oihana.xyz/FeeUnresolvedReason#UnknownPackageContent' ;
}
