<?php

namespace org\schema;

/**
 * The geographic shape of a place.
 * A GeoShape can be described using several properties whose values are based on latitude/longitude pairs. Either whitespace or commas can be used to separate latitude and longitude; whitespace should be used when writing a list of several such points.
 * @see https://schema.org/GeoCircle
 */
class GeoCircle extends GeoShape
{
    /**
     * Indicates the GeoCoordinates at the centre of a GeoShape, e.g. GeoCircle.
     */
    public null|GeoCoordinates $geoMidpoint ;

    /**
     * The country. Recommended to be in 2-letter ISO 3166-1 alpha-2 format, for example "US". For backward compatibility, a 3-letter ISO 3166-1 alpha-3 country code such as "SGP" or a full country name such as "Singapore" can also be used.
     */
    public null|float|int|Distance $geoRadius ;
}