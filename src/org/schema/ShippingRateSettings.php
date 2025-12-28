<?php

namespace org\schema;

use org\schema\places\DefinedRegion;

/**
 * ShippingService represents the criteria used to determine if and how an offer could be shipped to a customer.
 */
class ShippingRateSettings extends StructuredValue
{
    /**
     * Indicates when shipping to a particular shippingDestination is not available.
     */
    public null|bool $doesNotShip ;

    /**
     * A monetary value above (or at) which the shipping rate becomes free.
     * Intended to be used via an OfferShippingDetails with shippingSettingsLink matching this ShippingRateSettings.
     */
    public null|MonetaryAmount|DeliveryChargeSpecification $freeShippingThreshold ;

    /**
     * This can be marked 'true' to indicate that some published DeliveryTimeSettings or ShippingRateSettings are intended to apply to all OfferShippingDetails published by the same merchant, when referenced by a shippingSettingsLink in those settings. It is not meaningful to use a 'true' value for this property alongside a transitTimeLabel (for DeliveryTimeSettings) or shippingLabel (for ShippingRateSettings), since this property is for use with unlabelled settings.
     */
    public null|bool $isUnlabelledFallback ;

    /**
     * Fraction of the value of the order that is charged as shipping cost.
     * @var float|int|null
     */
    public null|float|int $orderPercentage ;

    /**
     * Indicates (possibly multiple) shipping destinations.
     * These can be defined in several ways, e.g. postalCode ranges.
     * @var null|array|DefinedRegion
     */
    public null|array|DefinedRegion $shippingDestination ;

    /**
     * The shipping rate is the cost of shipping to the specified destination.
     * Typically, the maxValue and currency values (of the MonetaryAmount) are most appropriate.
     * @var MonetaryAmount|ShippingRateSettings|null
     */
    public null|MonetaryAmount|ShippingRateSettings $shippingRate ;

    /**
     * Fraction of the weight that is used to compute the shipping price.
     * @var float|int|null
     */
    public null|float|int $weightPercentage ;
}


