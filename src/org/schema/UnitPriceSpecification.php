<?php

namespace org\schema;

/**
 * The price asked for a given offer by the respective organization or person.
 * @see https://schema.org/UnitPriceSpecification
 */
class UnitPriceSpecification extends PriceSpecification
{
    /**
     * Specifies for how long this price (or price component) will be billed.
     * Can be used, for example, to model the contractual duration of a subscription or payment plan.
     * Type can be either a Duration or a Number (in which case the unit of measurement, for example month, is specified by the unitCode property).
     * @var Duration|int|float|QuantitativeValue|null
     */
    public null|Duration|int|float|QuantitativeValue $billingDuration ;

    /**
     * This property specifies the minimal quantity and rounding increment that will be the basis for the billing.
     * The unit of measurement is specified by the unitCode property.
     * @var int|null
     */
    public null|int $billingIncrement ;

    /**
     * Specifies after how much time this price (or price component) becomes valid and billing starts.
     * Can be used, for example, to model a price increase after the first year of a subscription.
     * The unit of measurement is specified by the unitCode property.
     * @var int|null
     */
    public null|int $billingStart ;

    /**
     * Identifies a price component (for example, a line item on an invoice), part of the total price for an offer.
     * Example :
     * - ActivationFee
     * - CleaningFee
     * - DistanceFee
     * - Downpayment
     * - Installment
     * - Subscription
     * @var string|Enumeration|DefinedTerm|null
     * @see
     */
    public null|string|Enumeration|DefinedTerm $priceComponentType ;

    /**
     * Defines the type of a price specified for an offered product,
     * for example a list price, a (temporary) sale price or a manufacturer suggested retail price.
     * If multiple prices are specified for an offer the priceType property can be used to identify the type of each such specified price.
     * The value of priceType can be specified as a value from enumeration PriceTypeEnumeration or as a free form text string for price types that are not already predefined in PriceTypeEnumeration.
     * Example:
     * - ActivationFee
     * - CleaningFee
     * - DistanceFee
     * - Downpayment
     * - Installment
     * - Subscription
     * @var string|Enumeration|DefinedTerm|null
     * @see https://schema.org/PriceTypeEnumeration
     */
    public null|string|Enumeration|DefinedTerm $priceType ;

    /**
     * The reference quantity for which a certain price applies, e.g. 1 EUR per 4 kWh of electricity.
     * This property is a replacement for unitOfMeasurement for the advanced cases where the price does not relate to a standard unit.
     * @var QuantitativeValue|null
     */
    public null|QuantitativeValue $referenceQuantity ;

    /**
     * The unit of measurement given using the UN/CEFACT Common Code (3 characters) or a URL.
     * Other codes than the UN/CEFACT Common Code may be used with a prefix followed by a colon.
     * @var null|string
     */
    public ?string $unitCode ;

    /**
     * A string or text indicating the unit of measurement.
     * Useful if you cannot provide a standard unit code for unitCode.
     * @var null|string
     */
    public ?string $unitText ;
}