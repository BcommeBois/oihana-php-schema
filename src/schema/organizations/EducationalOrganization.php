<?php

namespace org\schema\organizations;

use org\schema\Organization;
use org\schema\Person;
use org\schema\traits\PlaceTrait;

/**
 * An educational organization.
 * https://schema.org/EducationalOrganization
 */
class EducationalOrganization extends Organization
{
    /**
     * The alumni(es) of an organization.
     * @var array|Person|null
     */
    public null|array|Person $alumni ;

    /**
     * The general opening hours for a business. Opening hours can be specified as a weekly time range, starting with days, then times per day. Multiple days can be listed with commas ',' separating each day. Day or time ranges are specified using a hyphen '-'.
     * @var string|null
     */
    public ?string $openingHours ;

    use PlaceTrait ;
}


