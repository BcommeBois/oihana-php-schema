<?php

namespace org\schema\traits ;

trait PostalAddressTrait
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
     * An address extension such as an apartment number, C/O or alternative name.
     * @var null|string
     */
    public ?string $extendedAddress ;

    /**
     * The post office box number for PO box addresses.
     * @var null|string
     */
    public ?string $postOfficeBoxNumber ;

    /**
     * The postal code. For example, 94043.
     * @var null|string
     */
    public ?string $postalCode ;

    /**
     * The street address. For example, 1600 Amphitheatre Pkwy.
     * @var null|string
     */
    public ?string $streetAddress ;
}