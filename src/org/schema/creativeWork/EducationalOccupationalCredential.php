<?php

namespace org\schema\creativeWork;

use org\schema\CreativeWork;
use org\schema\DefinedTerm;
use org\schema\Duration;
use org\schema\Organization;
use org\schema\places\AdministrativeArea;

/**
 * An educational or occupational credential. A diploma, academic degree, certification, qualification, badge, etc.,
 * that may be awarded to a person or other entity that meets the requirements defined by the credentialer.
 *
 * @see https://schema.org/EducationalOccupationalCredential
 */
class EducationalOccupationalCredential extends CreativeWork
{
    /**
     * Knowledge, skill, ability or personal attribute that must be demonstrated by a person or other entity in order
     * to do something such as earn an Educational Occupational Credential or understand a LearningResource.
     * @var null|string|DefinedTerm|array
     */
    public null|string|DefinedTerm|array $competencyRequired ;

    /**
     * The category or type of credential being described,
     * for example "degree”, “certificate”, “badge”, or more specific term.
     * @var null|string|DefinedTerm|array
     */
    public null|string|DefinedTerm|array $credentialCategory ;

    /**
     * An organization that acknowledges the validity, value or utility of a credential.
     *
     * Note: recognition may include a process of quality assurance or accreditation.
     *
     * @var Organization|array|string|null
     */
    public null|array|Organization|string $recognizedBy ;

    /**
     * The duration of validity of a permit or similar thing.
     * @var Duration|int|float|null
     */
    public null|Duration|int|float $validFor ;

    /**
     * The geographic area where the item is valid. Applies for example to a Permit, a Certification, or an EducationalOccupationalCredential.
     * @var null|AdministrativeArea
     */
    public null|AdministrativeArea $validIn ;
}