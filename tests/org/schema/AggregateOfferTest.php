<?php

namespace tests\org\schema ;

use ReflectionException;

use org\schema\AggregateOffer;
use org\schema\constants\Prop;
use org\schema\Thing;

use PHPUnit\Framework\TestCase;

class AggregateOfferTest extends TestCase
{
    public function testConstructorWithNoArguments()
    {
        $thing = new AggregateOffer();
        $this->assertObjectHasProperty( Prop::NAME , $thing );
        $this->assertNull( $thing->name ?? null , 'The name property must be null by default');
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorInitializesProperties()
    {
        $thing = new AggregateOffer( ['name' => 'Prix public'  ] );
        $this->assertSame('Prix public' , $thing->name );
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeIncludesContextAndType()
    {
        $thing = new AggregateOffer( ['name' => 'Prix public' ] );

        $data = $thing->jsonSerialize();

        // echo var_dump($data) . PHP_EOL;

        $this->assertArrayHasKey(Prop::AT_TYPE    , $data ) ;
        $this->assertArrayHasKey(Prop::AT_CONTEXT , $data ) ;

        $this->assertEquals('Prix public' , $data[ Prop::NAME ] ) ;

        $this->assertEquals('AggregateOffer', $data[ Prop::AT_TYPE ] ) ;
        $this->assertEquals(Thing::CONTEXT , $data[ Prop::AT_CONTEXT ] ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonEncode()
    {
        $thing = [ new AggregateOffer( ['name' => 'Prix public' ] ) ];

        $json = json_encode( $thing , JSON_UNESCAPED_SLASHES );

        $this->assertEquals('[{"@type":"AggregateOffer","@context":"https://schema.org","name":"Prix public"}]' , $json ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testToJsonSchema()
    {
        $schema = AggregateOffer::jsonSchema();

        // echo PHP_EOL . toPhpString
        // (
        //     $schema ,
        //     [
        //         'inline'        => false ,
        //         'useBrackets'   => true,
        //         'humanReadable' => true,
        //         'quote'         => 'double'
        //     ]
        // )
        // . PHP_EOL . PHP_EOL ;

        $expected =
        [
            '$id'                  => "https://schema.org/AggregateOffer" ,
            '$schema'              => "http://json-schema.org/draft-07/schema#" ,
            "type"                 => "object" ,
            "title"                => "AggregateOffer" ,
            "additionalProperties" => false ,
            "properties" =>
            [
                "_from" => [
                    "description" => "The metadata to indicates the edge 'from' identifier.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "_id" => [
                    "description" => "The metadata identifier of the item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "_key" => [
                    "description" => "The metadata unique key identifier of the thing.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "_rev" => [
                    "description" => "The metadata revision value of the thing.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "_to" => [
                    "description" => "The metadata to indicates the edge 'to' identifier.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "acceptedPaymentMethod" => [
                    "description" => "The payment method(s) that are accepted in general by an organization, or for some specific demand or offer.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "active" => [
                    "description" => "The active flag.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "boolean"
                        ]
                    ]
                ],
                "additionalProperty" => [
                    "description" => "A property-value pair representing an additional characteristic of the entity, e.g. a product feature or another characteristic for which there is no matching property in schema.org.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "additionalType" => [
                    "description" => "An additionalType for the item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "addon" => [
                    "description" => "An additional offer that can only be obtained in combination with the first base offer (e.g. supplements and extensions that are available for a surcharge).",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "advanceBookingRequirement" => [
                    "description" => "Type: QuantitativeValue",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ]
                    ]
                ],
                "aggregateRating" => [
                    "description" => "The overall rating, based on a collection of reviews or ratings, of the item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "alternateName" => [
                    "description" => "An alias for the item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "areaServed" => [
                    "description" => "The geographic area where a service or offered item is provided.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "asin" => [
                    "description" => "An Amazon Standard Identification Number (ASIN) is a 10-character alphanumeric unique identifier assigned by Amazon.com and its partners for product identification within the Amazon organization.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "availability" => [
                    "description" => "The availability of this item—for example In stock, Out of stock, Pre-order, etc.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "availabilityEnds" => [
                    "description" => "The end of the availability of the product or service included in the offer.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "availabilityStarts" => [
                    "description" => "The beginning of the availability of the product or service included in the offer.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "availableAtOrFrom" => [
                    "description" => "The place(s) from which the offer can be obtained (e.g. store locations).",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "availableDeliveryMethod" => [
                    "description" => "The delivery method(s) available for this offer.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "businessFunction" => [
                    "description" => "The business function (e.g. sell, lease, repair, dispose) of the offer or component of a bundle (TypeAndQuantityNode).",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "category" => [
                    "description" => "A category for the item. Greater signs or slashes can be used to informally indicate a category hierarchy.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "checkoutPageURLTemplate" => [
                    "description" => "A URL template (RFC 6570) for a checkout page for an offer.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "created" => [
                    "description" => "Date of creation of the resource.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "deliveryLeadTime" => [
                    "description" => "Type: QuantitativeValue",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ]
                    ]
                ],
                "description" => [
                    "description" => "A short description of the item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "disambiguatingDescription" => [
                    "description" => "A sub property of description. A short description of the item used to disambiguate from other, similar items. Information from other properties (in particular, name) may be necessary for the description to be useful for disambiguation.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "eligibleCustomerType" => [
                    "description" => "The type(s) of customers for which the given offer is valid.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "eligibleDuration" => [
                    "description" => "Type: QuantitativeValue",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ]
                    ]
                ],
                "eligibleQuantity" => [
                    "description" => "Type: QuantitativeValue",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ]
                    ]
                ],
                "eligibleRegion" => [
                    "description" => "The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is valid.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "eligibleTransactionVolume" => [
                    "description" => "Type: PriceSpecification",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ]
                    ]
                ],
                "gtin" => [
                    "description" => "A correct gtin value should be a valid GTIN, which means that it should be an all-numeric string of either 8, 12, 13 or 14 digits, or a \"GS1 Digital Link\" URL based on such a string.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "gtin12" => [
                    "description" => "The GTIN-12 code of the product, or the product to which the offer refers.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "gtin13" => [
                    "description" => "The GTIN-13 code of the product, or the product to which the offer refers.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "gtin14" => [
                    "description" => "The GTIN-14 code of the product, or the product to which the offer refers.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "gtin8" => [
                    "description" => "The GTIN-8 code of the product, or the product to which the offer refers. This code is also known as EAN/UCC-8 or 8-digit EAN.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "hasAdultConsideration" => [
                    "description" => "Used to tag an item to be intended or suitable for consumption or use by adults only.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "hasGS1DigitalLink" => [
                    "description" => "The GS1 digital link associated with the object.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "hasMeasurement" => [
                    "description" => "A measurement of an item, For example, the inseam of pants, the wheel size of a bicycle, the gauge of a screw, or the carbon footprint measured for certification by an authority.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "hasMerchantReturnPolicy" => [
                    "description" => "Specifies a MerchantReturnPolicy that may be applicable.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "hasPart" => [
                    "description" => "Indicates an item that this part of this item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "highPrice" => [
                    "description" => "The highest price of all offers available.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ],
                        [
                            "type" => "integer"
                        ],
                        [
                            "type" => "number"
                        ]
                    ]
                ],
                "id" => [
                    "description" => "The unique identifier of the item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ],
                        [
                            "type" => "integer"
                        ]
                    ]
                ],
                "identifier" => [
                    "description" => "The identifier of the item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "image" => [
                    "description" => "The image reference of this resource.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "includesObject" => [
                    "description" => "This links to a node or nodes indicating the exact quantity of the products included in an Offer or ProductCollection.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "ineligibleRegion" => [
                    "description" => "The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is not valid, e.g. a region where the transaction is not allowed.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "inventoryLevel" => [
                    "description" => "Type: QuantitativeValue",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ]
                    ]
                ],
                "isFamilyFriendly" => [
                    "description" => "Indicates whether this content is family friendly.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "boolean"
                        ]
                    ]
                ],
                "isPartOf" => [
                    "description" => "Indicates an item that this item is part of.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "itemCondition" => [
                    "description" => "A predefined value from OfferItemCondition specifying the condition of the product or service, or the products or services included in the offer.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ]
                    ]
                ],
                "itemOffered" => [
                    "description" => "An item being offered (or demanded). The transactional nature of the offer or demand is documented using businessFunction, e.g. sell, lease etc.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "leaseLength" => [
                    "description" => "Length of the lease for some Accommodation, either particular to some Offer or in some cases intrinsic to the property.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ]
                    ]
                ],
                "license" => [
                    "description" => "A legal document giving official permission to do something with the resource.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "lowPrice" => [
                    "description" => "The lowest price of all offers available.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ],
                        [
                            "type" => "integer"
                        ],
                        [
                            "type" => "number"
                        ]
                    ]
                ],
                "mobileUrl" => [
                    "description" => "The mobileUrl property is provided for specific situations in which data consumers need to determine whether one of several provided URLs is a dedicated 'mobile site'.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "modified" => [
                    "description" => "Date on which the resource was changed.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "mpn" => [
                    "description" => "The Manufacturer Part Number (MPN) of the product, or the product to which the offer refers.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "name" => [
                    "description" => "The name of the item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ],
                        [
                            "type" => "integer"
                        ]
                    ]
                ],
                "offerCount" => [
                    "description" => "he number of offers for the product.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "integer"
                        ]
                    ]
                ],
                "offeredBy" => [
                    "description" => "A pointer to the organization or person making the offer.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "offers" => [
                    "description" => "An offer to provide this item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "price" => [
                    "description" => "The offer price of a product, or of a price component when attached to PriceSpecification and its subtypes.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ],
                        [
                            "type" => "number"
                        ]
                    ]
                ],
                "priceCurrency" => [
                    "description" => "The currency (in 3-letter ISO 4217 format) of the price or a price component, when attached to PriceSpecification and its subtypes.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "priceSpecification" => [
                    "description" => "One or more detailed price specifications, indicating the unit price and delivery or payment charges.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "priceValidUntil" => [
                    "description" => "The date after which the price is no longer available.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ],
                        [
                            "type" => "integer"
                        ]
                    ]
                ],
                "publisher" => [
                    "description" => "The publisher of the resource.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "review" => [
                    "description" => "A review of the item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "seller" => [
                    "description" => "An entity which offers (sells / leases / lends / loans) the services / goods.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ],
                        [
                            "type" => "string"
                        ],
                        [
                            "type" => "integer"
                        ]
                    ]
                ],
                "serialNumber" => [
                    "description" => "The serial number or any alphanumeric identifier of a particular product.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "shippingDetails" => [
                    "description" => "Indicates information about the shipping policies and options associated with an Offer.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "array"
                        ]
                    ]
                ],
                "sku" => [
                    "description" => "The Stock Keeping Unit (SKU), i.e. a merchant-specific identifier for a product or service, or the product to which the offer refers.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ],
                "url" => [
                    "description" => "URL of the item.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ],
                        [
                            "type" => "integer"
                        ]
                    ]
                ],
                "validForMemberTier" => [
                    "description" => "Type: MemberProgramTier",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ]
                    ]
                ],
                "validFrom" => [
                    "description" => "The date when the item becomes valid.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ],
                        [
                            "type" => "integer"
                        ]
                    ]
                ],
                "validThrough" => [
                    "description" => "The end of the validity of offer, price specification, or opening hours data.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "string"
                        ],
                        [
                            "type" => "integer"
                        ]
                    ]
                ],
                "warranty" => [
                    "description" => "The warranty promise(s) included in the offer.",
                    "oneOf" => [
                        [
                            "type" => "null"
                        ],
                        [
                            "type" => "object"
                        ],
                        [
                            "type" => "string"
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals( $expected , $schema ) ;
    }
}

