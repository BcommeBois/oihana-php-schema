<?php

namespace org\schema;

/**
 * A DefinedRegion is a geographic area defined by potentially arbitrary
 * (rather than political, administrative or natural geographical) criteria.
 *
 * Properties are provided for defining a region by reference to sets of postal codes.
 *
 * @see https://schema.org/DefinedRegion
 */
class DefinedRegion extends ContactPoint
{
    /**
     * The country.
     * Recommended to be in 2-letter ISO 3166-1 alpha-2 format, for example "US".
     * For backward compatibility, a 3-letter ISO 3166-1 alpha-3 country code such as "SGP" or a full country name such as "Singapore" can also be used.
     * @var null|string
     */
    public ?string $addressCountry ;

    /**
     * The locality in which the street address is, and which is in the region. For example, Mountain View.
     * @var null|string
     */
    public ?string $addressLocality ;

    /**
     * The region in which the locality is, and which is in the country.
     * For example, California or another appropriate first-level Administrative division.
     * @var null|string
     */
    public ?string $addressRegion ;

    /**
     * The postal code. For example, 94043.
     * @var null|string
     */
    public ?string $postalCode ;

    /**
     * A defined range of postal codes indicated by a common textual prefix. Used for non-numeric systems such as UK.
     * @var null|string
     */
    public ?string $postalCodePrefix ;

    /**
     * A defined range of postal codes.
     * @var null|string|array|PostalCodeRangeSpecification
     */
    public null|string|array|PostalCodeRangeSpecification $postalCodeRange ;


}