<?php

namespace org\schema\constants\traits;

/**
 * The property names of the {@see \org\schema\ParcelDelivery} type.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package org\schema\constants\traits
 * @since   1.4.0
 */
trait ParcelDelivery
{
    const string DELIVERY_ADDRESS        = 'deliveryAddress' ;
    const string DELIVERY_STATUS         = 'deliveryStatus' ;
    const string EXPECTED_ARRIVAL_FROM   = 'expectedArrivalFrom' ;
    const string EXPECTED_ARRIVAL_UNTIL  = 'expectedArrivalUntil' ;
    const string HAS_DELIVERY_METHOD     = 'hasDeliveryMethod' ;
    const string ITEM_SHIPPED            = 'itemShipped' ;
    const string ORIGIN_ADDRESS          = 'originAddress' ;
    const string PART_OF_ORDER           = 'partOfOrder' ;
    const string REQUESTED_DELIVERY_DATE = 'requestedDeliveryDate' ;
    const string TRACKING_NUMBER         = 'trackingNumber' ;
    const string TRACKING_URL            = 'trackingUrl' ;

    /**
     * Also declared — with this very value — by the Action, CreativeWork,
     * FinancialIncentive, Invoice, Offer, Organization and Service traits this
     * one sits alongside in {@see Properties}. Identical redeclarations compose
     * without conflict ; any other value would be a fatal error.
     */
    const string PROVIDER = 'provider' ;
}
