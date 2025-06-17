<?php

namespace org\schema\traits;

use org\schema\AggregateRating;
use org\schema\creativeWork\Certification;
use org\schema\creativeWork\Map;
use org\schema\creativeWork\medias\ImageObject;
use org\schema\creativeWork\Photograph;
use org\schema\DefinedTerm;
use org\schema\Demand;
use org\schema\Event;
use org\schema\GeoCoordinates;
use org\schema\GeoShape;
use org\schema\LocationFeatureSpecification;
use org\schema\Offer;
use org\schema\OpeningHoursSpecification;
use org\schema\Place;
use org\schema\PostalAddress;
use org\schema\PropertyValue;
use org\schema\Review;
use org\schema\Service;
use org\schema\creativeWork\Website;

/**
 * Entities that have a somewhat fixed, physical extension.
 * @see https://schema.org/Place
 */
trait PlaceTrait
{
    /**
     * The additional description of the place.
     * Note : this property is a custom attribute of the original Place class defined in http://schema.org/Place.
     */
    public object|string|null $additional ;

    /**
     * Physical address of the item (PostalAddress or any object to describe it).
     */
    public PostalAddress|string|null $address = null ;

    /**
     * An amenity feature (e.g. a characteristic or service) of the Accommodation.
     * This generic property does not make a statement about whether the feature is included in an offer for the main accommodation or available at extra costs.
     * @var array|LocationFeatureSpecification|null
     */
    public null|array|LocationFeatureSpecification $amenityFeature ;

    /**
     * The overall rating, based on a collection of reviews or ratings, of the item.
     * @var array|AggregateRating|null
     */
    public null|array|AggregateRating $aggregateRating ;

    /**
     * A short textual code (also called "store code") that uniquely identifies a place of business.
     * The code is typically assigned by the parentOrganization and used in structured URLs.
     * For example, in the URL http://www.starbucks.co.uk/store-locator/etc/detail/3047 the code "3047" is a branchCode for a particular branch.
     * @var string|null
     */
    public ?string $branchCode ;

    /**
     * The basic containment relation between a place and another that it contains.
     */
    public array|Place|null $containsPlace ;

    /**
     * The basic containment relation between a place and another that it contains.
     */
    public array|Place|null $containedInsPlace ;

    /**
     * The Email address.
     */
    public null|string|PropertyValue|array $email ;

    /**
     * Upcoming or past events associated with this place or organization (legacy spelling; see singular form, event).
     * @var null|array|Event
     */
    public null|array|Event $event ;

    /**
     * The fax number.
     * @var string|null|array|PropertyValue
     */
    public null|string|PropertyValue|array $faxNumber ;

    /**
     * The geo coordinates of the place (GeoShape or GeoCoordinates).
     * @var null|GeoCoordinates|GeoShape
     */
    public null|GeoCoordinates|GeoShape $geo ;

    use GeospatialGeometryTrait ;

    /**
     * Certification information about a product, organization, service, place, or person.
     * @var array|Certification|null
     */
    public null|array|Certification $hasCertification ;

    /**
     * Indicates whether some facility (e.g. FoodEstablishment, CovidTestingFacility) offers a service that can be used by driving through in a car. In the case of CovidTestingFacility such facilities could potentially help with social distancing from other potentially-infected users.
     * @var bool|null
     */
    public ?bool $hasDriveThroughService ;

    /**
     * A URL to a map of the place.
     * @var string|Map|null
     */
    public null|string|Map $hasMap ;

    /**
     * Photographs of this place (legacy spelling; see singular form, photo).
     */
    public ?array $images ;

    /**
     * A flag to signal that the item, event, or place is accessible for free.
     * @var bool|null
     */
    public ?bool $isAccessibleForFree ;

    /**
     * The International Standard of Industrial Classification of All Economic Activities (ISIC), Revision 4 code for a particular organization, business person, or place.
     * @var string|null
     */
    public ?string $isicV4 ;

    /**
     * Keywords or tags used to describe some item. Multiple textual entries in a keywords list are typically delimited by commas, or by repeating the property.
     * @var string|DefinedTerm|array|null
     */
    public null|string|DefinedTerm|array $keywords ;

    /**
     * The latitude of a location. For example 37.42242 (WGS 84).
     * @see https://en.wikipedia.org/wiki/World_Geodetic_System
     */
    public null|int|float $latitude ;

    /**
     * An associated logo.
     */
    public null|string|ImageObject $logo ;

    /**
     * The longitude of a location. For example -122.08585 (WGS 84).
     * @see https://en.wikipedia.org/wiki/World_Geodetic_System
     */
    public null|int|float $longitude ;

    /**
     * The total number of individuals that may attend an event or venue.
     * Supersedes capacity
     * @var null|int|string
     */
    public null|int|string $maximumAttendeeCapacity ;

    /**
     * An offer to provide this item.
     * @var array|Offer|null|Demand
     */
    public array|Offer|Demand|null $offers ;

    /**
     * The opening hours of a certain place.
     */
    public null|array|OpeningHoursSpecification $openingHoursSpecification ;

    /**
     * Photographs of this place (legacy spelling; see singular form, photo).
     * A photograph of this place.
     */
    public null|array|ImageObject|Photograph $photo ;

    /**
     * A flag to signal that the Place is open to public visitors. If this property is omitted there is no assumed default boolean value.
     * @var bool|null
     */
    public ?bool $publicAccess ;

    /**
     * The number of the remaining attendee.
     * @var ?int
     */
    public ?int $remainingAttendee ;

    /**
     * A review of the item.
     * @var array|Review|null
     */
    public null|array|Review $review ;

    /**
     * The services provided by a place.
     */
    public null|array|Service $service ;

    /**
     * A slogan or motto associated with the item.
     * @var string|object|null
     */
    public object|string|null $slogan ;

    /**
     * Indicates whether it is allowed to smoke in the place, e.g. in the restaurant, hotel or hotel room.
     * @var bool|null
     */
    public ?bool $smokingAllowed ;

    /**
     * The special opening hours of a certain place.
     */
    public null|array|OpeningHoursSpecification $specialOpeningHoursSpecification ;

    /**
     * The telephone number.
     * @var string|null|array|PropertyValue
     */
    public null|string|PropertyValue|array $telephone ;

    /**
     * A page providing information on how to book a tour of some Place, such as an Accommodation or ApartmentComplex in a real estate setting, as well as other kinds of tours as appropriate.
     * @var null|string|object
     */
    public null|string|object $tourBookingPage ;
    /**
     * The collection of all websites of this place.
     */
    public array|null|string|WebSite $website ;
}


