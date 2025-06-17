<?php

namespace org\schema;

/**
 * Used to describe membership in a loyalty programs (e.g. "StarAliance"), traveler clubs (e.g. "AAA"), purchase clubs ("Safeway Club"), etc.
 * @see https://schema.org/ProgramMembership
 */
class ProgramMembership extends Intangible
{
    /**
     * The Organization (airline, travelers' club, retailer, etc.) the membership is made with or which offers the MemberProgram.
     * @var null|Organization
     */
    public null|Organization $hostingOrganization ;

    /**
     * A member of an Organization or a ProgramMembership. Organizations can be members of organizations; ProgramMembership is typically for individuals.
     * @var Organization|Person|null
     */
    public null|Organization|Person $member ;

    /**
     * A unique identifier for the membership.
     * @var string|null
     */
    public ?string $membershipNumber ;

    /**
     * The number of membership points earned by the member.
     * If necessary, the unitText can be used to express the units the points are issued in. (E.g. stars, miles, etc.)
     * @var int|QuantitativeValue|null
     */
    public null|int|QuantitativeValue $membershipPointsEarned ;

    /**
     * The MemberProgram associated with a ProgramMembership.
     * @var ?MemberProgram
     */
    public ?MemberProgram $program ;

    /**
     * The program providing the membership. It is preferable to use :program instead.
     * @var ?string
     */
    public ?string $programName ;
}