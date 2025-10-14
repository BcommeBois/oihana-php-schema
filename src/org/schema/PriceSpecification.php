<?php

namespace org\schema;

/**
 * A structured value representing a price or price range. Typically, only the subclasses of this type are used for markup. It is recommended to use MonetaryAmount to describe independent amounts of money such as a salary, credit
 * @see https://schema.org/PriceSpecification
 */
class PriceSpecification extends StructuredValue
{
    /**
     * The interval and unit of measurement of ordering quantities for which the offer or price specification is valid.
     * This allows e.g. specifying that a certain freight charge is valid only for a certain quantity.
     * @var QuantitativeValue|null
     */
    public null|QuantitativeValue $eligibleQuantity ;

    /**
     * The transaction volume, in a monetary unit, for which the offer or price specification is valid, e.g. for indicating a minimal purchasing volume, to express free shipping above a certain order volume, or to limit the acceptance of credit cards to purchases to a certain minimal amount.
     * @var PriceSpecification|null
     */
    public null|PriceSpecification $eligibleTransactionVolume ;

    /**
     * The highest price if the price is a range.
     * @var int|float|null
     */
    public null|int|float $maxPrice ;

    /**
     * The number of membership points earned by the member.
     * If necessary, the unitText can be used to express the units the points are issued in. (E.g. stars, miles, etc.)
     * @var int|float|QuantitativeValue|null
     */
    public null|int|float|QuantitativeValue $membershipPointsEarned ;

    /**
     * The lowest price if the price is a range.
     * @var int|float|null
     */
    public null|int|float $minPrice ;

    /**
     * The offer price of a product, or of a price component when attached to PriceSpecification and its subtypes.
     * @var int|float|string|null
     */
    public null|int|float|string $price ;

    /**
     * he currency of the price, or a price component when attached to PriceSpecification and its subtypes.
     * @var string|null
     */
    public null|string $priceCurrency ;

    /**
     * The membership program tier an Offer (or a PriceSpecification, OfferShippingDetails, or MerchantReturnPolicy under an Offer) is valid for.
     * @var MemberProgramTier|null
     */
    public ?MemberProgramTier $validForMemberTier ;

    /**
     * The date when the item becomes valid.
     * @var string|int|null
     */
    public null|string|int $validFrom ;

    /**
     * The date after when the item is not valid. For example the end of an offer, salary period, or a period of opening hours.
     * @var string|int|null
     */
    public null|string|int $validThrough ;

    /**
     * Specifies whether the applicable value-added tax (VAT) is included in the price specification or not.
     * @var bool|null
     */
    public null|bool $valueAddedTaxIncluded ;
}