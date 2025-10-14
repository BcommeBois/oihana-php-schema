<?php

namespace org\schema;

use org\schema\enumerations\BusinessEntityType;
use org\schema\enumerations\BusinessFunction;
use org\schema\enumerations\DeliveryMethod;
use org\schema\enumerations\OfferItemCondition;
use org\schema\places\AdministrativeArea;
use org\schema\services\LoanOrCredit;

/**
 * An event happening at a certain time and location, such as a concert, lecture, or festival.
 * Repeated events may be structured as separate Event objects.
 * see https://schema.org/Demand
 */
class Demand extends Intangible
{
    /**
     * The payment method(s) that are accepted in general by an organization, or for some specific demand or offer.
     * @var null|string|array|PaymentMethod|LoanOrCredit
     */
    public null|string|array|PaymentMethod|LoanOrCredit $acceptedPaymentMethod ;

    /**
     * The amount of time that is required between accepting the offer and the actual usage of the resource or service.
     * @var QuantitativeValue|null
     */
    public ?QuantitativeValue $advanceBookingRequirement ;

    /**
     * The geographic area where a service or offered item is provided.
     */
    public null|string|Place|GeoShape|AdministrativeArea $areaServed ;

    /**
     * An Amazon Standard Identification Number (ASIN) is a 10-character alphanumeric unique identifier assigned by Amazon.com and its partners for product identification within the Amazon organization.
     */
    public null|string $asin ;

    /**
     * The availability of this item—for example In stock, Out of stock, Pre-order, etc.
     */
    public string|object|null $availability ;

    /**
     * The end of the availability of the product or service included in the offer.
     */
    public ?string $availabilityEnds ;

    /**
     * The beginning of the availability of the product or service included in the offer.
     */
    public ?string $availabilityStarts ;

    /**
     * The place(s) from which the offer can be obtained (e.g. store locations).
     */
    public null|array|Place $availableAtOrFrom ;

    /**
     * The delivery method(s) available for this offer.
     */
    public null|array|DeliveryMethod $availableDeliveryMethod ;

    /**
     * The business function (e.g. sell, lease, repair, dispose) of the offer or component of a bundle (TypeAndQuantityNode).
     * The default is http://purl.org/goodrelations/v1#Sell.
     * Commonly used values:
     * - http://purl.org/goodrelations/v1#ConstructionInstallation
     * - http://purl.org/goodrelations/v1#Dispose
     * - http://purl.org/goodrelations/v1#LeaseOut
     * - http://purl.org/goodrelations/v1#Maintain
     * - http://purl.org/goodrelations/v1#ProvideService
     * - http://purl.org/goodrelations/v1#Repair
     * - http://purl.org/goodrelations/v1#Sell
     * - http://purl.org/goodrelations/v1#Buy
     */
    public null|array|BusinessFunction|DefinedTerm $businessFunction ;

    /**
     * The typical delay between the receipt of the order and the goods either leaving the warehouse or being prepared for pickup, in case the delivery method is on site pickup.
     * @var QuantitativeValue|null
     */
    public null|QuantitativeValue $deliveryLeadTime ;

    /**
     * The type(s) of customers for which the given offer is valid.
     * @var null|array|BusinessEntityType|DefinedTerm
     */
    public null|array|BusinessEntityType|DefinedTerm $eligibleCustomerType ;

    /**
     * The duration for which the given offer is valid.
     * @var QuantitativeValue|null
     */
    public null|QuantitativeValue $eligibleDuration ;

    /**
     * The interval and unit of measurement of ordering quantities for which the offer or price specification is valid.
     * This allows e.g. specifying that a certain freight charge is valid only for a certain quantity.
     * @var QuantitativeValue|null
     */
    public null|QuantitativeValue $eligibleQuantity ;

    /**
     * The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is valid.
     */
    public null|string|Place|GeoShape $eligibleRegion ;

    /**
     * The transaction volume, in a monetary unit, for which the offer or price specification is valid, e.g. for indicating a minimal purchasing volume, to express free shipping above a certain order volume, or to limit the acceptance of credit cards to purchases to a certain minimal amount.
     * @var PriceSpecification|null
     */
    public null|PriceSpecification $eligibleTransactionVolume ;

    /**
     * A correct gtin value should be a valid GTIN, which means that it should be an all-numeric string of either 8, 12, 13 or 14 digits, or a "GS1 Digital Link" URL based on such a string.
     * The numeric component should also have a valid GS1 check digit and meet the other rules for valid GTINs.
     * @var string|null
     */
    public ?string $gtin ;

    /**
     * The GTIN-12 code of the product, or the product to which the offer refers.
     * The GTIN-12 is the 12-digit GS1 Identification Key composed of a U.P.C. Company Prefix, Item Reference, and Check Digit used to identify trade items.
     * @var string|null
     */
    public ?string $gtin12 ;

    /**
     * The GTIN-13 code of the product, or the product to which the offer refers.
     * This is equivalent to 13-digit ISBN codes and EAN UCC-13. Former 12-digit UPC codes can be converted into a GTIN-13 code by simply adding a preceding zero.
     * @var string|null
     */
    public ?string $gtin13 ;

    /**
     * The GTIN-14 code of the product, or the product to which the offer refers.
     * @var string|null
     */
    public ?string $gtin14 ;

    /**
     * The GTIN-8 code of the product, or the product to which the offer refers. This code is also known as EAN/UCC-8 or 8-digit EAN.
     * @var string|null
     */
    public ?string $gtin8 ;

    /**
     * This links to a node or nodes indicating the exact quantity of the products included in an Offer or ProductCollection.
     * @var array|TypeAndQuantityNode|null
     */
    public null|array|TypeAndQuantityNode $includesObject ;

    /**
     * The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is not valid, e.g. a region where the transaction is not allowed.
     */
    public null|string|Place|GeoShape $ineligibleRegion ;

    /**
     * The current approximate inventory level for the item or items.
     * @var QuantitativeValue|null
     */
    public null|QuantitativeValue $inventoryLevel ;

    /**
     * A predefined value from OfferItemCondition specifying the condition of the product or service, or the products or services included in the offer.
     * Also used for product return policies to specify the condition of products accepted for returns.
     * @var OfferItemCondition|DefinedTerm|null
     */
    public null|OfferItemCondition|DefinedTerm $itemCondition ;

    /**
     * An item being offered (or demanded). The transactional nature of the offer or demand is documented using businessFunction, e.g. sell, lease etc.
     * While several common expected types are listed explicitly in this definition, others can be used.
     * Using a second type, such as Product or a subtype of Product, can clarify the nature of the offer.
     * @var array|CreativeWork|Event|Product|Service|null
     */
    public null|array|CreativeWork|Event|Product|Service $itemOffered ;

    /**
     * The Manufacturer Part Number (MPN) of the product, or the product to which the offer refers.
     * @var string|null
     */
    public ?string $mpn ;

    /**
     * One or more detailed price specifications, indicating the unit price and delivery or payment charges.
     * @var null|array|PriceSpecification
     */
    public null|array|PriceSpecification $priceSpecification ;

    /**
     * An entity which offers (sells / leases / lends / loans) the services / goods.
     * A seller may also be a provider.
     */
    public null|Organization|Person $seller ;

    /**
     * The serial number or any alphanumeric identifier of a particular product.
     * When attached to an offer, it is a shortcut for the serial number of the product included in the offer.
     */
    public ?string $serialNumber ;

    /**
     * The Stock Keeping Unit (SKU), i.e. a merchant-specific identifier for a product or service, or the product to which the offer refers.
     */
    public ?string $sku ;

    /**
     * The date when the item becomes valid.
     */
    public null|string|int $validFrom ;

    /**
     * The end of the validity of offer, price specification, or opening hours data.
     */
    public null|string|int $validThrough ;

    /**
     * The end of the validity of offer, price specification, or opening hours data.
     */
    public null|string|DefinedTerm|WarrantyPromise $warranty ;
}