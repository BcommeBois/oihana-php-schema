<?php

namespace org\schema;

/**
 * A MemberProgramTier specifies a tier under a loyalty (member) program, for example "gold".
 * @see https://schema.org/MemberProgramTier
 */
class MemberProgramTier extends Intangible
{
    /**
     * A member benefit for a particular tier of a loyalty program.
     * Example :
     * - TierBenefitLoyaltyPoints
     * - TierBenefitLoyaltyPrice
     * - TierBenefitLoyaltyReturns
     * - TierBenefitLoyaltyShipping
     * @var null|array|Enumeration|DefinedTerm
     */
    public null|array|Enumeration|DefinedTerm $hasTierBenefit ;

    /**
     * A requirement for a user to join a membership tier,
     * for example:
     * - A CreditCard if the tier requires sign up for a credit card,
     * - A UnitPriceSpecification if the user is required to pay a (periodic) fee,
     * - A MonetaryAmount if the user needs to spend a minimum amount to join the tier.
     * If a tier is free to join then this property does not need to be specified.
     * @var null|array|string|MonetaryAmount|UnitPriceSpecification
     */
    public null|array|string|MonetaryAmount|UnitPriceSpecification $hasTierRequirement ;
}