<?php

namespace org\schema;

use org\schema\places\Country;

/**
 * The geographic coordinates of a place or event.
 */
class GeoCoordinates extends Thing
{
    /**
     * Physical address of the item.
     */
    public null|string|array|PostalAddress $address ;

    /**
     * The country. Recommended to be in 2-letter ISO 3166-1 alpha-2 format, for example "US". For backward compatibility, a 3-letter ISO 3166-1 alpha-3 country code such as "SGP" or a full country name such as "Singapore" can also be used.
     */
    public null|string|Country $addressCountry ;

    /**
     * The distance of a location. For example 125 (double).
     */
    public null|int|float $distance ;

    /**
     * The elevation of a location (WGS 84). Values may be of the form 'NUMBER UNIT_OF_MEASUREMENT' (e.g., '1,000 m', '3,200 ft') while numbers alone should be assumed to be a value in meters.
     * @see https://en.wikipedia.org/wiki/World_Geodetic_System
     */
    public null|int|float $elevation ;

    /**
     * The latitude of a location. For example 37.42242 (WGS 84).
     * @see https://en.wikipedia.org/wiki/World_Geodetic_System
     */
    public null|int|float $latitude ;

    /**
     * The longitude of a location. For example -122.08585 (WGS 84).
     * @see https://en.wikipedia.org/wiki/World_Geodetic_System
     */
    public null|int|float $longitude ;

    /**
     * The postal code. For example, 94043.
     */
    public ?string $postalCode ;
}