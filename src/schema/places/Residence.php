<?php

namespace org\schema\places;

use org\schema\Place;

/**
 * The place where a person lives.
 * @see https://schema.org/Residence
 */
class Residence extends Place
{
    /**
     * The A floor plan of some Accommodation.
     * @var null|object
     */
    public ?object $accommodationFloorPlan ; // TODO FloorPlan

    // TODO complete
}


