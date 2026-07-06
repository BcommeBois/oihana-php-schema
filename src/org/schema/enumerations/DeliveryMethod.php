<?php

namespace org\schema\enumerations;

use org\schema\Enumeration;

/**
 * A delivery method is a standardized procedure for transferring the product or service to the destination of fulfillment chosen by the customer. Delivery methods are characterized by the means of transportation used, and by the organization or group that is the contracting party for the sending organization or person.
 * Commonly used values:
 * - http://purl.org/goodrelations/v1#DeliveryModeDirectDownload
 * - http://purl.org/goodrelations/v1#DeliveryModeFreight
 * - http://purl.org/goodrelations/v1#DeliveryModeMail
 * - http://purl.org/goodrelations/v1#DeliveryModeOwnFleet
 * - http://purl.org/goodrelations/v1#DeliveryModePickUp
 * - http://purl.org/goodrelations/v1#DHL
 * - http://purl.org/goodrelations/v1#FederalExpress
 * - http://purl.org/goodrelations/v1#UPS
 */
class DeliveryMethod extends Enumeration
{
    /**
     * Delivery by the DHL carrier.
     */
    public const string DHL = 'http://purl.org/goodrelations/v1#DHL' ;

    /**
     * Delivery as a direct download over the network.
     */
    public const string DIRECT_DOWNLOAD = 'http://purl.org/goodrelations/v1#DeliveryModeDirectDownload' ;

    /**
     * Delivery by the Federal Express carrier.
     */
    public const string FEDERAL_EXPRESS = 'http://purl.org/goodrelations/v1#FederalExpress' ;

    /**
     * Delivery by freight transportation.
     */
    public const string FREIGHT = 'http://purl.org/goodrelations/v1#DeliveryModeFreight' ;

    /**
     * Delivery by postal mail.
     */
    public const string MAIL = 'http://purl.org/goodrelations/v1#DeliveryModeMail' ;

    /**
     * Pickup of the item at the seller's site.
     */
    public const string ON_SITE_PICKUP = 'http://purl.org/goodrelations/v1#DeliveryModePickUp' ;

    /**
     * Delivery by the sender's own fleet.
     */
    public const string OWN_FLEET = 'http://purl.org/goodrelations/v1#DeliveryModeOwnFleet' ;

    /**
     * Delivery by the UPS carrier.
     */
    public const string UPS = 'http://purl.org/goodrelations/v1#UPS' ;
}


