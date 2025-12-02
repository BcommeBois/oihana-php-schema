<?php

namespace org\schema;

use org\schema\creativeWork\EducationalOccupationalCredential;
use org\schema\places\AdministrativeArea;

/**
 * A profession, may involve prolonged training and/or a formal qualification.
 * @see https://schema.org/Occupation
 */
class Occupation extends Intangible
{
    /**
     * Educational background needed for the position or Occupation.
     * @var string|array|EducationalOccupationalCredential|null
     */
    public null|string|array|EducationalOccupationalCredential $educationRequirements ;

    /**
     * An estimated salary for a job posting or occupation, based on a variety of variables including,
     * but not limited to industry, job title, and location.
     *
     * Estimated salaries are often computed by outside organizations rather than the hiring organization,
     * who may not have committed to the estimated value.
     *
     * @var string|array|MonetaryAmount|float|int|null
     */
    public null|string|array|MonetaryAmount|float|int $estimatedSalary ;

    /**
     * Description of skills and experience needed for the position or Occupation.
     * @var string|array|OccupationExperienceRequirements|null
     */
    public null|string|array|OccupationExperienceRequirements $experienceRequirements ;

    /**
     * The region/country for which this occupational description is appropriate.
     * Note that educational requirements and qualifications can vary between jurisdictions.
     * @var array|string|AdministrativeArea|null
     */
    public null|array|string|AdministrativeArea $occupationLocation ;

    /**
     * Specific qualifications required for this role or Occupation.
     * @var string|array|EducationalOccupationalCredential|null
     */
    public null|string|array|EducationalOccupationalCredential $qualifications ;

    /**
     * Responsibilities associated with this role or Occupation.
     * @var string|array|null
     */
    public null|string|array $responsibilities ;


    /**
     * A statement of knowledge, skill, ability, task or any other assertion expressing a competency
     * that is either claimed by a person, an organization or desired or required
     * to fulfill a role or to work in an occupation.
     * @var string|array|null|DefinedTerm
     */
    public null|string|array|DefinedTerm $skills ;
}