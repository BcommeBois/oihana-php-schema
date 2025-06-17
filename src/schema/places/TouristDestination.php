<?php

namespace org\schema\places;

use org\schema\Audience;
use org\schema\Place;

/**
 * A tourist destination. In principle any Place can be a TouristDestination from a City, Region or Country to an AmusementPark or Hotel.
 * @see https://schema.org/TouristDestination
 */
class TouristDestination extends Place
{
    /**
     * Attraction located at destination.
     * @var array|TouristAttraction|null
     */
    public null|array|TouristAttraction $includesAttraction ;

    /**
     * Attraction suitable for type(s) of tourist. E.g. children, visitors from a particular country, etc.
     */
    public null|array|Audience|string $touristType ;
}


