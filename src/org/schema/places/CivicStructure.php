<?php

namespace org\schema\places;

use org\schema\Place;

/**
 * A public structure, such as a town hall or concert hall.
 * @see https://schema.org/CivicStructure
 */
class CivicStructure extends Place
{
    /**
     * The general opening hours for a business. Opening hours can be specified as a weekly time range, starting with days, then times per day. Multiple days can be listed with commas ',' separating each day. Day or time ranges are specified using a hyphen '-'.
     * @var string|null
     */
    public ?string $openingHours ;
}


