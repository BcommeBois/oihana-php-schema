<?php

namespace org\schema\traits ;

use org\schema\enumerations\ContactPointOption;
use org\schema\GeoShape;
use org\schema\OpeningHoursSpecification;
use org\schema\Place;
use org\schema\places\AdministrativeArea;
use org\schema\Product;
use org\schema\Service;

/**
 * A contact point—for example, a Customer Complaints department.
 * @see https://schema.org/ContactPoint
 */
trait ContactPointTrait
{
    /**
     * The geographic area where a service or offered item is provided.
     * @var null|string|Place|GeoShape|AdministrativeArea|array
     */
    public null|string|Place|GeoShape|AdministrativeArea|array $areaServed ;
    
    /**
     * A language someone may use with or at the item, service or place.
     * Please use one of the language codes from the IETF BCP 47 standard.
     * @var array|string|null
     */
    public array|string|null $availableLanguage ;

    /**
     * An option available on this contact point (e.g. a toll-free number or support for hearing-impaired callers).
     * @var null|string|array|ContactPointOption
     */
    public null|string|array|ContactPointOption $contactOption ;

    /**
     * A person or organization can have different contact points, for different purposes.
     * For example, a sales contact point, a PR contact point and so on. This property is used to specify the kind of contact point.
     * @var string|null
     */
    public ?string $contactType ;

    /**
     * The email address.
     * @var null|string|array
     */
    public null|string|array $email ;

    /**
     * The fax number.
     * @var null|string|array
     */
    public null|string|array $faxNumber ;

    /**
     * The hours during which this service or contact is available.
     * @var null|array|OpeningHoursSpecification|string
     */
    public null|array|OpeningHoursSpecification|string $hoursAvailable ;

    /**
     * The product or service this support contact point is related to (such as product support for a particular product line).
     * @var null|string|Product|Service|array
     */
    public null|string|Product|Service|array $productSupported ;

    /**
     * The telephone number.
     * @var null|string|array
     */
    public null|string|array $telephone ;
}