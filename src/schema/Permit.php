<?php

namespace org\schema;

use DateTime;
use org\schema\places\AdministrativeArea;

/**
 * A permit issued by an organization, e.g. a parking pass.
 * @see https://schema.org/Permit
 */
class Permit extends Intangible
{
    /**
     * The organization issuing the item, for example a Permit, Ticket, or Certification.
     * @var Organization|null
     */
    public ?Organization $issuedBy ;

    /**
     * The service through which the permit was granted.
     * @var Service|null
     */
    public ?Service $issuedThrough ;

    /**
     * The target audience for this permit.
     * @var array|Audience|null
     */
    public null|array|Audience $permitAudience ;

    /**
     * The duration of validity of a permit or similar thing.
     * @var Duration|int|float|null
     */
    public null|Duration|int|float $validFor ;

   /**
    * The date when the item becomes valid (DateTime).
    * @var string|int|DateTime|null
    */
   public null|string|int|DateTime $validFrom ;

   /**
    * The geographic area where the item is valid. Applies for example to a Permit, a Certification, or an EducationalOccupationalCredential.
    * @var null|AdministrativeArea
    */
    public null|AdministrativeArea $validIn ;

    /**
     * The date when the item is no longer valid.
     * @var string|int|DateTime|null
     */
    public null|string|int|DateTime $validUntil ;
}