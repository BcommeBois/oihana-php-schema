<?php

namespace org\schema;

use oihana\reflect\attributes\HydrateWith;

use org\schema\creativeWork\Certification;
use org\schema\creativeWork\medias\ImageObject;

/**
 * Any offered product or service. For example: a pair of shoes; a concert ticket;
 * the rental of a car; a haircut; or an episode of a TV show streamed online.
 * @see https://schema.org/Product
 */
class Product extends Thing
{
    /**
     * A property-value pair representing an additional characteristic of the entity,
     * e.g. a product feature or another characteristic for which there is no matching property in schema.org.
     */
    #[HydrateWith( PropertyValue::class ) ]
    public null|array|PropertyValue $additionalProperty = null ;

    /**
     * The overall rating, based on a collection of reviews or ratings, of the item.
     */
    #[HydrateWith( AggregateRating::class ) ]
    public null|array|AggregateRating $aggregateRating ;

    /**
     * An Amazon Standard Identification Number (ASIN) is a 10-character alphanumeric unique identifier assigned by Amazon.com and its partners for product identification within the Amazon organization.
     */
    public null|string $asin ;

    /**
     * An intended audience, i.e. a group for whom something was created. Supersedes serviceAudience.
     */
    #[HydrateWith( Audience::class ) ]
    public null|array|Audience $audience ;

    /**
     * An award won by or for this item.
     * @var string|array|null
     */
    public null|string|array $award ;

    /**
     * The brand(s) associated with a product or service, or the brand(s) maintained by an organization or business person.
     * @var Brand|Organization|array|null
     */
    public Brand|Organization|array|null $brand ;

    /**
     * A category for the item. Greater signs or slashes can be used to informally indicate a category hierarchy.
     * @var null|array|string|CategoryCode|Thing
     */
    public null|array|string|CategoryCode|Thing $category ;

    /**
     * The color of the product.
     * @var string|null
     */
    public ?string $color ;

    /**
     * A color swatch image, visualizing the color of a Product
     * @var string|object|null
     */
    public string|object|null $colorSwatch ;

    /**
     * The depth of the item.
     * @var float|null
     */
    public ?float $depth ;

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
     * @var string|Enumeration|DefinedTerm|array|null
     */
    public null|string|Enumeration|DefinedTerm|array $hasAdultConsideration ;

    /**
     * Certification information about a product, organization, service, place, or person.
     */
    #[HydrateWith( Certification::class ) ]
    public null|array|Certification $hasCertification ;

    /**
     * A measurement of an item, For example, the inseam of pants, the wheel size of a bicycle, the gauge of a screw, or the carbon footprint measured for certification by an authority.
     * Usually an exact measurement, but can also be a range of measurements for adjustable products, for example belts and ski bindings.
     */
    #[HydrateWith( QuantitativeValue::class ) ]
    public null|array|QuantitativeValue $hasMeasurement ;

    /**
     * The height of the item.
     * @var float|null
     */
    public ?float $height ;

    /**
     * Indicates the productGroupID for a ProductGroup that this product isVariantOf.
     * @var string|null
     */
    public ?string $inProductGroupWithID ;

    /**
     * A pointer to another product (or multiple products) for which this product is an accessory or spare part.
     */
    #[HydrateWith(Product::class)]
    public null|Product|array $isAccessoryOrSparePartFor ;

    /**
     * A pointer to another product (or multiple products) for which this product is a consumable.
     */
    #[HydrateWith( Product::class )]
    public null|Product|array $isConsumableFor ;

    /**
     * Indicates whether this content is family friendly.
     * @var bool|null
     */
    public ?bool $isFamilyFriendly ;

    /**
     * A pointer to another, somehow related product (or multiple products).
     * @var Product|Service|array|null
     */
    public null|Product|Service|array $isRelatedTo ;

    /**
     * A pointer to another, functionally similar product (or multiple products).
     * @var Product|Service|array|null
     */
    public null|Product|Service|array $isSimilarTo ;

    /**
     * Indicates the kind of product that this is a variant of. In the case of ProductModel, this is a pointer (from a ProductModel) to a base product from which this product is a variant.
     */
    #[HydrateWith( ProductModel::class , ProductGroup::class ) ]
    public null|array|ProductModel|ProductGroup $isVariantOf ;

    /**
     * A predefined value from OfferItemCondition specifying the condition of the product or service, or the products or services included in the offer.
     * Also used for product return policies to specify the condition of products accepted for returns.
     * Example:
     * - DamagedCondition
     * - NewCondition
     * - RefurbishedCondition
     * - UsedCondition
     * @var DefinedTerm|string|Enumeration|null
     */
    public null|DefinedTerm|string|Enumeration $itemCondition ;

    /**
     * Keywords or tags used to describe some item. Multiple textual entries in a keywords list are typically delimited by commas, or by repeating the property.
     * @var string|DefinedTerm|array|null
     */
    public null|string|DefinedTerm|array $keywords ;

    /**
     * An associated logo.
     * @var string|ImageObject|null
     */
    public null|string|ImageObject $logo ;

    /**
     * The manufacturer of the product.
     * @var int|Organization|string|null
     */
    public int|Organization|string|null $manufacturer ;

    /**
     * A material that something is made from, e.g. leather, wool, cotton, paper.
     * @var array|Product|string|null
     */
    public array|Product|string|null $material ;

    /**
     * The mobileUrl property is provided for specific situations in which data consumers need to determine whether one of several provided URLs is a dedicated 'mobile site'.
     * @var ?string
     */
    public ?string $mobileUrl ;

    /**
     * The model of the product.
     * @var ProductModel|string|null
     */
    public null|ProductModel|string $model ;

    /**
     * The Manufacturer Part Number (MPN) of the product, or the product to which the offer refers.
     * @var string|null
     */
    public ?string $mpn ;

    /**
     * Indicates the NATO stock number (nsn) of a Product.
     * @var string|null
     * @see https://en.wikipedia.org/wiki/NATO_Stock_Number
     */
    public ?string $nsn ;

    /**
     * An offer to provide this item.
     */
    #[HydrateWith(Offer::class, Demand::class)]
    public array|Offer|Demand|null $offers ;

    /**
     * A pattern that something has, for example 'polka dot', 'striped', 'Canadian flag'. Values are typically expressed as text, although links to controlled value schemes are also supported.
     * @var string|DefinedTerm|null
     */
    public null|string|DefinedTerm $pattern ;

    /**
     * The product identifier, such as ISBN. For example: meta itemprop="productID" content="isbn:123-456-789".
     * @var ?string
     */
    public ?string $productID ;

    /**
     * The date of production of the item, e.g. vehicle.
     * @var string|null|int
     */
    public null|string|int $productionDate ;

    /**
     * The date the item, e.g. vehicle, was purchased by the current owner.
     * @var string|int|null
     */
    public null|string|int $purchaseDate ;

    /**
     * The release date of a product or product model.
     * This can be used to distinguish the exact variant of a product.
     * @var string|int|null
     */
    public null|string|int $releaseDate ;

    /**
     * A review of the item.
     */
    #[HydrateWith( Review::class )]
    public null|array|Review $review ;

    /**
     * A standardized size of a product or creative work,
     * specified either through a simple textual string (for example 'XL', '32Wx34L'),
     * a QuantitativeValue with a unitCode,
     * or a comprehensive and structured SizeSpecification;
     * in other cases, the width, height, depth and weight properties may be more applicable.
     */
    #[HydrateWith( DefinedTerm::class , QuantitativeValue::class , SizeSpecification::class ) ]
    public null|string|DefinedTerm|QuantitativeValue|SizeSpecification|array $size ;

    /**
     * The Stock Keeping Unit (SKU), i.e. a merchant-specific identifier for a product or service, or the product to which the offer refers.
     */
    public ?string $sku ;

    /**
     * A slogan or motto associated with the item.
     * @var string|null
     */
    public ?string $slogan ;

    /**
     * The weight of the item.
     * @var float|null
     */
    public ?float $weight ;

    /**
     * The width of the item.
     * @var float|null
     */
    public ?float $width ;
}