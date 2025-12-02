<?php

namespace org\schema;

/**
 * Indicates employment-related experience requirements, e.g. monthsOfExperience.
 *
 * @see https://schema.org/OccupationalExperienceRequirements
 */
class OccupationExperienceRequirements extends Intangible
{
    /**
     * Indicates the minimal number of months of experience required for a position.
     * @var null|int|float
     */
    public null|int|float $monthsOfExperience ;
}