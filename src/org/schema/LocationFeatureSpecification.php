<?php

namespace org\schema;

use DateTime;

/**
 * Specifies a location feature by providing a structured value representing a feature of an accommodation as a property-value pair of varying degrees of formality.
 * @see https://schema.org/LocationFeatureSpecification
 */
class LocationFeatureSpecification extends PropertyValue
{
    /**
     * The hours during which this service or contact is available.
     * @var null|array|OpeningHoursSpecification
     */
    public null|array|OpeningHoursSpecification $hoursAvailable ;

    /**
     * The date when the item becomes valid (DateTime).
     */
    public null|string|int|DateTime $validFrom ;

    /**
     * The date after when the item is not valid. For example the end of an offer, salary period, or a period of opening hours.
     */
    public null|string|int|DateTime $validThrough ;
}