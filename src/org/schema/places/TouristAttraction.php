<?php

namespace org\schema\places;

use org\schema\Audience;
use org\schema\Place;

/**
 * A particular physical business or branch of an organization.
 * Examples of LocalBusiness include a restaurant, a particular branch of a restaurant chain, a branch of a bank, a medical practice, a club, a bowling alley, etc.
 * @see https://schema.org/LocalBusiness
 */
class TouristAttraction extends Place
{
    /**
     * A language someone may use with or at the item, service or place. Please use one of the language codes from the IETF BCP 47 standard.
     * @var string|array|null
     */
    public null|array|string $availableLanguage ;

    /**
     * Attraction suitable for type(s) of tourist. E.g. children, visitors from a particular country, etc.
     */
    public null|array|Audience|string $touristType ;
}


