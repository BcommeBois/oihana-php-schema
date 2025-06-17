<?php

namespace org\schema;

/**
 * A MemberProgram defines a loyalty (or membership) program that provides its members with certain benefits, for example better pricing, free shipping or returns, or the ability to earn loyalty points.
 * Member programs may have multiple tiers, for example silver and gold members, each with different benefits.
 * @see https://schema.org/MemberProgram
 */
class MemberProgram extends Intangible
{
    /**
     * The tiers of a member program.
     * @var null|array|MemberProgramTier
     */
    public null|array|MemberProgramTier $hasTiers ;

    /**
     * The Organization (airline, travelers' club, retailer, etc.) the membership is made with or which offers the MemberProgram.
     * @var null|Organization
     */
    public null|Organization $hostingOrganization ;
}