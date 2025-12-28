<?php

namespace org\schema;

use org\schema\places\DefinedRegion;

/**
 * ShippingConditions represent a set of constraints and information about the conditions of shipping a product.
 * Such conditions may apply to only a subset of the products being shipped, depending on aspects of the product like weight, size, price, destination, and others. All the specified conditions must be met for this ShippingConditions to apply.
 * @see https://schema.org/ShippingConditions
 */
class ShippingConditions extends StructuredValue
{
    /**
     * The depth of the item.
     */
    public null|int|float|QuantitativeValue|Distance $depth ;

    /**
     * Indicates when shipping to a particular shippingDestination is not available.
     */
    public null|bool $doesNotShip ;

    /**
     * The height of the item.
     */
    public null|int|float|QuantitativeValue|Distance $height ;

    /**
     * Limits the number of items being shipped for which these conditions apply.
     * @var QuantitativeValue|int|null
     */
    public null|int|QuantitativeValue $numItems ;

    /**
     * Minimum and maximum order value for which these shipping conditions are valid.
     * @var MonetaryAmount|null
     */
    public null|MonetaryAmount $orderValue ;

    /**
     * Limited period during which these shipping conditions apply.
     * @var OpeningHoursSpecification|null
     */
    public null|OpeningHoursSpecification $seasonalOverride;

    /**
     * Indicates (possibly multiple) shipping destinations.
     * These can be defined in several ways, e.g. postalCode ranges.
     * @var DefinedRegion|array|null
     */
    public null|array|DefinedRegion $shippingDestination ;

    /**
     * Indicates the origin of a shipment, i.e. where it should be coming from.
     * @var DefinedRegion|null
     */
    public null|DefinedRegion $shippingOrigin ;

    /**
     * The shipping rate is the cost of shipping to the specified destination.
     * Typically, the maxValue and currency values (of the MonetaryAmount) are most appropriate.
     * @var MonetaryAmount|ShippingRateSettings|null
     */
    public null|MonetaryAmount|ShippingRateSettings $shippingRate ;

    /**
     * The typical delay the order has been sent for delivery and the goods reach the final customer.
     * In the context of ShippingDeliveryTime, use the QuantitativeValue. Typical properties: minValue, maxValue, unitCode (d for DAY).
     * In the context of ShippingConditions, use the ServicePeriod. It has a duration (as a QuantitativeValue) and also business days and a cut-off time.
     */
    public null|int|float|QuantitativeValue|ServicePeriod $transitTime ;

    /**
     * The weight of the item.
     */
    public null|int|float|QuantitativeValue|Mass $weight ;

    /**
     * The width of the item.
     */
    public null|int|float|QuantitativeValue|Distance $width ;
}


