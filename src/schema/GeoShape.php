<?php

namespace org\schema;

use org\schema\places\Country;

/**
 * The geographic shape of a place.
 * A GeoShape can be described using several properties whose values are based on latitude/longitude pairs. Either whitespace or commas can be used to separate latitude and longitude; whitespace should be used when writing a list of several such points.
 * @see https://schema.org/GeoShape
 */
class GeoShape extends StructuredValue
{
    /**
     * Physical address of the item.
     */
    public null|string|PostalAddress $address ;

    /**
     * The country. Recommended to be in 2-letter ISO 3166-1 alpha-2 format, for example "US". For backward compatibility, a 3-letter ISO 3166-1 alpha-3 country code such as "SGP" or a full country name such as "Singapore" can also be used.
     */
    public null|string|Country $addressCountry ;

    /**
     * A box is the area enclosed by the rectangle formed by two points.
     * The first point is the lower corner, the second point is the upper corner.
     * A box is expressed as two points separated by a space character.
     */
    public ?string $box ;

    /**
     * A circle is the circular region of a specified radius centered at a specified latitude and longitude.
     * A circle is expressed as a pair followed by a radius in meters.
     */
    public ?string $circle ;

    /**
     * The elevation of a location (WGS 84).
     * Values may be of the form 'NUMBER UNIT_OF_MEASUREMENT' (e.g., '1,000 m', '3,200 ft') while numbers alone should be assumed to be a value in meters.
     */
    public null|string|float|int $elevation ;

    /**
     * A line is a point-to-point path consisting of two or more points. A line is expressed as a series of two or more point objects separated by space.
     */
    public ?string $line ;

    /**
     * A polygon is the area enclosed by a point-to-point path for which the starting and ending points are the same.
     * A polygon is expressed as a series of four or more space delimited points where the first and final points are identical.
     */
    public ?string $polygon ;

    /**
     * The postal code. For example, 94043.
     */
    public ?string $postalCode ;
}