<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;
use org\schema\DefinedTerm;
use org\schema\Enumeration;

/**
 * A map.
 * @see https://schema.org/Map
 */
class Map extends CreativeWork
{
    /**
     * Indicates the kind of Map, from the MapCategoryType Enumeration.
     * Example :
     * - ParkingMap
     * - SeatingMap
     * - TransitMap
     * - VenueMap
     * @var string|Enumeration|DefinedTerm|null
     * @see https://schema.org/MapCategoryType
     */
    public null|string|Enumeration|DefinedTerm $mapType ;
}


