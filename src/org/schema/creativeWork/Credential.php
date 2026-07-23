<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;
use org\schema\DefinedTerm;
use org\schema\Duration;
use org\schema\Organization;
use org\schema\places\AdministrativeArea;

/**
 * A credential is a certificate that is used to verify the identity of a person or entity.
 * @see https://schema.org/Credential
 */
class Credential extends CreativeWork
{
    /**
     * The category or type of credential being described, for example "degree”, “certificate”, “badge”, or more specific term.
     * @var null|string|array|DefinedTerm
     */
    public null|string|array|DefinedTerm $credentialCategory ;

    /**
     * An organization that acknowledges the validity, value or utility of a credential.
     * Note: recognition may include a process of quality assurance or accreditation.
     * @var null|string|array|Organization
     */
    public null|string|array|Organization $recognizedBy ;

    /**
     * The duration of validity of a permit or similar thing.
     * @var null|string|array|int|float|Duration
     */
    public null|string|array|int|float|Duration $validFor ;

    /**
     * The geographic area where the item is valid. Applies for example to a Permit, a Certification, or an EducationalOccupationalCredential.
     * @var null|string|array|AdministrativeArea
     */
    public null|string|array|AdministrativeArea $validIn ;
}
