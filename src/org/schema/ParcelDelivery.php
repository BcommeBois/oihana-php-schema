<?php

namespace org\schema;

use org\schema\enumerations\DeliveryMethod;
use org\schema\events\DeliveryEvent;

/**
 * The delivery of a parcel either via the postal service or a commercial service.
 * @see https://schema.org/ParcelDelivery
 */
class ParcelDelivery extends Intangible
{
    /**
     * Destination address.
     *
     * Accepts a raw associative array so a row read straight from storage can be
     * assigned as-is, exactly like {@see \xyz\oihana\schema\business\documents\BusinessDocument::$billingAddress}.
     *
     * @var null|array|string|PostalAddress
     */
    public null|array|string|PostalAddress $deliveryAddress ;

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
     *
     * This property may be expressed as a predefined {@see DeliveryMethod}, a
     * {@see DefinedTerm} — which covers the house, priced delivery terms a back
     * office maintains in its own thesaurus — a string identifier, or a list of
     * such values. Same union as {@see \xyz\oihana\schema\places\Site::$deliveryMethod}
     * and {@see \xyz\oihana\schema\organizations\Company::$deliveryMethod}.
     *
     * @var null|array|string|DeliveryMethod|DefinedTerm
     */
    public null|array|string|DeliveryMethod|DefinedTerm $hasDeliveryMethod ;

    /**
     * Item(s) being shipped.
     * @var array|Product|null
     */
    public null|array|Product $itemShipped ;

    /**
     * Shipper's address.
     * @var null|string|array|PostalAddress
     */
    public null|string|array|PostalAddress $originAddress ;

    /**
     * The overall order the items in this delivery were included in.
     * @var null|array|Order
     */
    public null|array|Order $partOfOrder ;

    /**
     * The service provider, service operator, or service performer; the goods producer.
     * Another party (a seller) may offer those services or goods on behalf of the provider.
     * A provider may also serve as the seller.
     * @var null|array|Organization|Person
     */
    public null|array|Organization|Person $provider ;

    /**
     * The date the customer asks the parcel to be delivered on.
     *
     * This is an intent, stated when the delivery is ordered — not an estimate :
     * {@see ParcelDelivery::$expectedArrivalFrom} and
     * {@see ParcelDelivery::$expectedArrivalUntil} say when the parcel *may*
     * arrive, as bounded by the carrier, and stay free to express that window
     * later on, once the carrier has answered.
     *
     * @var null|string|int
     *
     * @since 1.4.0
     */
    public null|string|int $requestedDeliveryDate ;

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