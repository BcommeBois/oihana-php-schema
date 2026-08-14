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
     * @var null|array|Organization
     */
    public null|array|Organization $hostingOrganization ;

    /**
     * A member of an Organization or a ProgramMembership. Organizations can be members of organizations; ProgramMembership is typically for individuals.
     * @var Organization|Person|array|null
     */
    public null|array|Organization|Person $member ;

    /**
     * A unique identifier for the membership.
     * @var string|null
     */
    public ?string $membershipNumber ;

    /**
     * The number of membership points earned by the member.
     * If necessary, the unitText can be used to express the units the points are issued in. (E.g. stars, miles, etc.)
     * @var int|QuantitativeValue|array|null
     */
    public null|int|array|QuantitativeValue $membershipPointsEarned ;

    /**
     * The MemberProgram associated with a ProgramMembership.
     * @var null|array|MemberProgram
     */
    public null|array|MemberProgram $program ;

    /**
     * The program providing the membership. It is preferable to use :program instead.
     * @var string|null
     */
    public ?string $programName ;
}