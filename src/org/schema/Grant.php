<?php

namespace org\schema;

use DateTime;

/**
 * A grant, typically financial or otherwise quantifiable, of resources.
 *
 * Typically a funder sponsors some MonetaryAmount to an Organization or Person,
 * sometimes not necessarily via a dedicated or long-lived Project, resulting in one or more outputs, or fundedItems.
 *
 * For financial sponsorship, indicate the funder of a MonetaryGrant.
 * For non-financial support, indicate sponsor of Grants of resources (e.g. office space).
 *
 * Grants support activities directed towards some agreed collective goals, often but not always organized as Projects.
 * Long-lived projects are sometimes sponsored by a variety of grants over time, but it is also common
 * for a project to be associated with a single grant.
 *
 * The amount of a Grant is represented using amount as a MonetaryAmount.
 *
 * ### Example
 *
 * ```json
 * {
 *     "@context": "https://schema.org",
 *     "@type": "Grant",
 *     "name": "Horizon Europe Grant – AI for Sustainable Agriculture",
 *     "identifier": "HEU-AGRI-AI-2025-0142",
 *     "funder":
 *     {
 *         "@type": "Organization",
 *         "name": "European Commission"
 *     },
 *     "grantee":
 *     {
 *         "@type": "Organization",
 *         "name": "Université de Paris"
 *     },
 *     "fundedItem":
 *     {
 *          "@type": "ResearchProject",
 *          "name": "Intelligence Artificielle pour l'Agriculture Durable"
 *     },
 *     "amount":
 *     {
 *          "@type": "MonetaryAmount",
 *          "currency": "EUR",
 *          "value": 2500000
 *     },
 *     "startDate": "2025-01-01",
 *     "endDate": "2027-12-31"
 * }
 * ```
 *
 * @see http://schema.org/Grant
 */
class Grant extends Intangible
{
    /**
     * The amount of money.
     *
     * Note : not standard.
     *
     * @var int|float|MonetaryAmount|null
     */
    public null|int|float|MonetaryAmount $amount ;

    /**
     * The end date and time of the event or item (in ISO 8601 date format).
     *
     * Note : not standard.
     *
     * @var string|int|DateTime|null
     */
    public null|string|int|DateTime $endDate ;

    /**
     * Indicates something directly or indirectly funded or sponsored through a Grant.
     *
     * See also ownershipFundingInfo.
     *
     * @var null|Organization|Person|Product|Event|CreativeWork
     */
    public null|Organization|Person|Product|Event|CreativeWork $fundedItem ;

    /**
     * A person or organization that supports (sponsors) something through some kind of financial contribution.
     *
     * @var null|Organization|Person
     */
    public null|Organization|Person $funder ;

    /**
     * The person, organization, contact point, or audience that has been granted.
     *
     * Note : not standard.
     *
     * @var null|array|Audience|ContactPoint|Organization|Person
     */
    public null|array|Audience|ContactPoint|Organization|Person $grantee ;

    /**
     * A person or organization that supports a thing through a pledge, promise, or financial contribution.
     *
     * E.g. a sponsor of a Medical Study or a corporate sponsor of an event.
     *
     * @var null|Organization|Person
     */
    public null|Organization|Person $sponsor ;

    /**
     * The start date and time of the item (in ISO 8601 date format).
     *
     * Note : not standard.
     *
     * @var string|int|DateTime|null
     */
    public null|string|int|DateTime $startDate ;
}



