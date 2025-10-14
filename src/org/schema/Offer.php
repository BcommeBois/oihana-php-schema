<?php

namespace org\schema;

use org\schema\enumerations\AdultOrientedEnumeration;
use org\schema\enumerations\BusinessEntityType;
use org\schema\enumerations\BusinessFunction;
use org\schema\enumerations\DeliveryMethod;
use org\schema\enumerations\OfferItemCondition;
use org\schema\places\AdministrativeArea;
use org\schema\services\LoanOrCredit;

/**
 * An event happening at a certain time and location, such as a concert, lecture, or festival.
 * Repeated events may be structured as separate Event objects.
 * @see https://schema.org/Offer
 */
class Offer extends Intangible
{
    /**
     * The payment method(s) that are accepted in general by an organization, or for some specific demand or offer.
     * @var null|string|array|PaymentMethod|LoanOrCredit
     */
    public null|string|array|PaymentMethod|LoanOrCredit $acceptedPaymentMethod ;

    /**
     * A property-value pair representing an additional characteristic of the entity, e.g. a product feature or another characteristic for which there is no matching property in schema.org.
     */
    public null|array|PropertyValue $additionalProperty ;

    /**
     * An additional offer that can only be obtained in combination with the first base offer (e.g. supplements and extensions that are available for a surcharge).
     * @var array|Offer|null
     */
    public null|array|Offer $addon ;

    /**
     * The amount of time that is required between accepting the offer and the actual usage of the resource or service.
     * @var QuantitativeValue|null
     */
    public ?QuantitativeValue $advanceBookingRequirement ;

    /**
     * The overall rating, based on a collection of reviews or ratings, of the item.
     * @var array|AggregateRating|null
     */
    public null|array|AggregateRating $aggregateRating ;

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
    public null|array|BusinessFunction|DefinedTerm|string $businessFunction ;

    /**
     * A category for the item. Greater signs or slashes can be used to informally indicate a category hierarchy.
     * @see null|string|CategoryCode|Thing
     */
    public null|string|CategoryCode|Thing $category ;

    /**
     * A URL template (RFC 6570) for a checkout page for an offer.
     * This approach allows merchants to specify a URL for online checkout of the offered product, by interpolating parameters such as the logged in user ID, product ID, quantity, discount code etc.
     * Parameter naming and standardization are not specified here.
     * @var string|null
     */
    public ?string $checkoutPageURLTemplate ;

    /**
     * The typical delay between the receipt of the order and the goods either leaving the warehouse or being prepared for pickup, in case the delivery method is on site pickup.
     * @var QuantitativeValue|null
     */
    public null|QuantitativeValue $deliveryLeadTime ;

    /**
     * The type(s) of customers for which the given offer is valid.
     * @var null|array|BusinessEntityType|DefinedTerm|string
     */
    public null|array|BusinessEntityType|DefinedTerm|string $eligibleCustomerType ;

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
     * Used to tag an item to be intended or suitable for consumption or use by adults only.
     * Example:
     * - AlcoholConsideration
     * - DangerousGoodConsideration
     * - HealthcareConsideration
     * - NarcoticConsideration
     * - ReducedRelevanceForChildrenConsideration
     * - SexualContentConsideration
     * - TobaccoNicotineConsideration
     * - UnclassifiedAdultConsideration
     * - ViolenceConsideration
     * - WeaponConsideration
     * @var string|AdultOrientedEnumeration|DefinedTerm|array|null
     */
    public null|string|AdultOrientedEnumeration|DefinedTerm|array $hasAdultConsideration ;

    /**
     * The GS1 digital link associated with the object.
     * This URL should conform to the particular requirements of digital links. The link should only contain the Application Identifiers (AIs) that are relevant for the entity being annotated, for instance a Product or an Organization, and for the correct granularity.
     * @var string|null
     */
    public ?string $hasGS1DigitalLink ;

    /**
     * A measurement of an item, For example, the inseam of pants, the wheel size of a bicycle, the gauge of a screw, or the carbon footprint measured for certification by an authority.
     * Usually an exact measurement, but can also be a range of measurements for adjustable products, for example belts and ski bindings.
     * @var array|QuantitativeValue|null
     */
    public null|array|QuantitativeValue $hasMeasurement ;

    /**
     * Specifies a MerchantReturnPolicy that may be applicable.
     * @var null|array|MerchantReturnPolicy
     */
    public null|array|MerchantReturnPolicy $hasMerchantReturnPolicy ;

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
     * Indicates whether this content is family friendly.
     * @var bool|null
     */
    public ?bool $isFamilyFriendly ;

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
     * Length of the lease for some Accommodation, either particular to some Offer or in some cases intrinsic to the property.
     * @var QuantitativeValue|Duration|null
     */
    public null|QuantitativeValue|Duration $leaseLength ;

    /**
     * The mobileUrl property is provided for specific situations in which data consumers need to determine whether one of several provided URLs is a dedicated 'mobile site'.
     * @var ?string
     */
    public ?string $mobileUrl ;

    /**
     * The Manufacturer Part Number (MPN) of the product, or the product to which the offer refers.
     * @var string|null
     */
    public ?string $mpn ;

    /**
     * A pointer to the organization or person making the offer.
     * @var array|Organization|Person|null
     */
    public null|array|Organization|Person $offeredBy ;

    /**
     * The offer price of a product, or of a price component when attached to PriceSpecification and its subtypes.
     */
    public string|float|null $price ;

    /**
     * The currency (in 3-letter ISO 4217 format) of the price or a price component, when attached to PriceSpecification and its subtypes.
     */
    public string|object|null $priceCurrency ;

    /**
     * One or more detailed price specifications, indicating the unit price and delivery or payment charges.
     * @var null|array|PriceSpecification
     */
    public null|array|PriceSpecification $priceSpecification ;

    /**
     * The date after which the price is no longer available.
     * @var int|string|null
     */
    public null|string|int $priceValidUntil ;

    /**
     * A review of the item.
     * @var array|Review|null
     */
    public null|array|Review $review ;

    /**
     * An entity which offers (sells / leases / lends / loans) the services / goods.
     * A seller may also be a provider.
     */
    public null|Organization|Person|Thing|string|int|array $seller ;

    /**
     * The serial number or any alphanumeric identifier of a particular product.
     * When attached to an offer, it is a shortcut for the serial number of the product included in the offer.
     */
    public ?string $serialNumber ;

    /**
     * Indicates information about the shipping policies and options associated with an Offer.
     * @var array|OfferShippingDetails|null
     */
    public null|array|OfferShippingDetails $shippingDetails ;

    /**
     * The Stock Keeping Unit (SKU), i.e. a merchant-specific identifier for a product or service, or the product to which the offer refers.
     */
    public ?string $sku ;

    /**
     * The membership program tier an Offer (or a PriceSpecification, OfferShippingDetails, or MerchantReturnPolicy under an Offer) is valid for.
     * @var MemberProgramTier|null
     */
    public ?MemberProgramTier $validForMemberTier ;

    /**
     * The date when the item becomes valid.
     */
    public null|string|int $validFrom ;

    /**
     * The end of the validity of offer, price specification, or opening hours data.
     */
    public null|string|int $validThrough ;

    /**
     * The warranty promise(s) included in the offer.
     */
    public null|string|DefinedTerm|WarrantyPromise $warranty ;
}