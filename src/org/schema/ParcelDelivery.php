<?php

namespace org\schema;

use org\schema\enumerations\DeliveryMethod;
use org\schema\events\DeliveryEvent;

/**
 * An order is a confirmation of a transaction (a receipt), which can contain multiple line items, each represented by an Offer that has been accepted by the customer.
 * @see https://schema.org/Order
 */
class ParcelDelivery extends Intangible
{
    /**
     * The identifier of the order item.
     * @var null|string|PostalAddress
     */
    public null|string|PostalAddress $deliveryAddress ;

    /**
     * New entry added as the package passes through each leg of its journey (from shipment to final delivery).
     * @var DeliveryEvent|null|array
     */
    public null|array|DeliveryEvent $deliveryStatus ;

    /**
     * The earliest date the package may arrive.
     * @var null|string|int
     */
    public null|string|int $expectedArrivalFrom ;

    /**
     * The latest date the package may arrive.
     * @var null|string|int
     */
    public null|string|int $expectedArrivalUntil ;

    /**
     * Method used for delivery or shipping.
     * @var array|DeliveryMethod|null
     */
    public null|array|DeliveryMethod $hasDeliveryMethod ;

    /**
     * Item(s) being shipped.
     * @var array|Product|null
     */
    public null|array|Product $itemShipped ;

    /**
     * Shipper's address.
     * @var null|string|PostalAddress
     */
    public null|string|PostalAddress $originAddress ;

    /**
     * The overall order the items in this delivery were included in.
     * @var null|Order
     */
    public ?Order $partOfOrder ;

    /**
     * The service provider, service operator, or service performer; the goods producer.
     * Another party (a seller) may offer those services or goods on behalf of the provider.
     * A provider may also serve as the seller.
     * @var Organization|Person|null
     */
    public null|Organization|Person $provider ;

    /**
     * Shipper tracking number.
     * @var null|string
     */
    public ?string $trackingNumber ;

    /**
     * Tracking url for the parcel delivery.
     * @var null|string
     */
    public ?string $trackingUrl ;
}