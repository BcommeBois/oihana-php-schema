<?php

namespace org\schema;

/**
 * ShippingService represents the criteria used to determine if and how an offer could be shipped to a customer.
 * @see https://schema.org/ShippingService
 */
class ShippingService extends StructuredValue
{
    /**
     * Type of fulfillment applicable to the ShippingService.
     * @see https://schema.org/FulfillmentTypeEnumeration
     * - FulfillmentTypeCollectionPoint
     * - FulfillmentTypeDelivery
     * - FulfillmentTypePickupDropoff
     * - FulfillmentTypePickupInStore
     * - FulfillmentTypeScheduledDelivery
     */
    public null|string|Enumeration|DefinedTerm $fulfillmentType ;

    /**
     * The typical delay between the receipt of the order and the goods either leaving the warehouse or being prepared for pickup,
     * in case the delivery method is on site pickup.
     * @var ServicePeriod|QuantitativeValue|null
     */
    public null|ServicePeriod|QuantitativeValue $handlingTime ;

    /**
     * The conditions (constraints, price) applicable to the ShippingService.
     * @var null|array|ShippingConditions
     */
    public null|array|ShippingConditions $shippingConditions ;

    /**
     * The membership program tier an Offer (or a PriceSpecification, OfferShippingDetails, or MerchantReturnPolicy under an Offer) is valid for.
     * @var MemberProgramTier|null
     */
    public ?MemberProgramTier $validForMemberTier ;
}


