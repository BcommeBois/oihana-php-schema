<?php

namespace org\schema;

use org\schema\places\DefinedRegion;

/**
 * OfferShippingDetails represents information about shipping destinations.
 * @see https://schema.org/OfferShippingDetails
 */
class OfferShippingDetails extends StructuredValue
{
    /**
     * The total delay between the receipt of the order and the goods reaching the final customer.
     * @var ShippingDeliveryTime|null
     */
    public null|ShippingDeliveryTime $deliveryTime ;

    /**
     * The depth of the item.
     */
    public null|int|float|QuantitativeValue|Distance $depth ;

    /**
     * Indicates when shipping to a particular shippingDestination is not available.
     */
    public null|bool $doesNotShip ;

    /**
     * Specification of a shipping service offered by the organization.
     * @var array|ShippingService|null
     */
    public null|array|ShippingService $hasShippingService ;

    /**
     * The height of the item.
     */
    public null|int|float|QuantitativeValue|Distance $height ;

    /**
     * Indicates (possibly multiple) shipping destinations.
     * These can be defined in several ways, e.g. postalCode ranges.
     * @var null|array|DefinedRegion
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
     * The membership program tier an Offer (or a PriceSpecification, OfferShippingDetails, or MerchantReturnPolicy under an Offer) is valid for.
     * @var MemberProgramTier|null
     */
    public ?MemberProgramTier $validForMemberTier ;

    /**
     * The weight of the item.
     */
    public null|int|float|QuantitativeValue|Mass $weight ;

    /**
     * The width of the item.
     */
    public null|int|float|QuantitativeValue|Distance $width ;
}