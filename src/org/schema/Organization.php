<?php

namespace org\schema;

use org\schema\creativeWork\Certification;
use org\schema\creativeWork\medias\ImageObject;
use org\schema\enumerations\NonprofitType;
use org\schema\services\LoanOrCredit;

/**
 * Entities that have a somewhat fixed, physical extension.
 * @see http://schema.org/Organization
 */
class Organization extends Thing
{
    /**
     * The payment method(s) that are accepted in general by an organization, or for some specific demand or offer.
     * @var null|string|array|PaymentMethod|LoanOrCredit
     */
    public null|string|array|PaymentMethod|LoanOrCredit $acceptedPaymentMethod ;

    /**
     * For a NewsMediaOrganization or other news-related Organization, a statement about public engagement activities (for news media, the newsroom’s), including involving the public - digitally or otherwise -- in coverage decisions, reporting and activities after publication.
     * @var string|CreativeWork|null
     */
    public null|string|CreativeWork $actionableFeedbackPolicy ;

    /**
     * The additional description of the organization.
     * Note : this property is a custom attribute of the original Organization class defined in http://schema.org/Organization.
     */
    public object|string|null $additional ;

    /**
     * Physical address of the item (PostalAddress or any object to describe it).
     */
    public null|string|array|PostalAddress $address = null ;

    /**
     * The number of completed interactions for this entity, in a particular role (the 'agent'), in a particular action (indicated in the statistic), and in a particular context (i.e. interactionService).
     * @var InteractionCounter|array|null
     */
    public null|array|InteractionCounter $agentInteractionStatistic ;

    /**
     * The overall rating, based on a collection of reviews or ratings, of the item.
     * @var array|AggregateRating|null
     */
    public null|array|AggregateRating $aggregateRating ;

    /**
     * Alumni of an organization.
     * @var array|Person|null
     */
    public null|array|Person $alumni ;

    /**
     * The ape identifier of the organization.
     * @var string|null
     */
    public ?string $ape ;

    /**
     * The geographic area where a service or offered item is provided.
     */
    public null|string|Place|GeoShape $areaServed ;

    /**
     * An award won by or for this item.
     * @var array|string|null
     */
    public null|array|string $award ;

    /**
     * The brand(s) associated with a product or service, or the brand(s) maintained by an organization or business person.
     * @var null|Brand|Organization
     */
    public null|Brand|Organization $brand ;

    /**
     * Certification information about a product, organization, service, place, or person.
     * @var array|Certification|null
     */
    public null|array|Certification $hasCertification ;

    /**
     * A contact point for a person or organization.
     */
    public null|ContactPoint|array $contactPoint ;

    /**
     * A relationship between an organization and a department of that organization,
     * also described as an organization (allowing different urls, logos, opening hours).
     * For example: a store with a pharmacy, or a bakery with a cafe.
     */
    public null|Organization|array $department  ;

    /**
     * The date that this organization was dissolved.
     * @var string|null
     */
    public null|string $dissolutionDate ;

    /**
     * The Dun & Bradstreet DUNS number for identifying an organization or business person.
     * @var string|null
     */
    public ?string $duns ;

    /**
     * The Email address.
     */
    public null|array|string|PropertyValue $email ;

    /**
     * The employees of the organization
     * @var null|array|Person
     */
    public null|array|Person $employees ;

    /**
     * Upcoming or past events associated with this organization (legacy spelling; see singular form, event).
     * @var null|array|Event
     */
    public null|array|Event $event ;

    /**
     * The fax number.
     * @var string|null|array|PropertyValue
     */
    public null|string|PropertyValue|array $faxNumber ;

    /**
     * A person or organization who founded this organization.
     * @var array|null|Organization|Person
     */
    public null|array|Organization|Person $founder ;

    /**
     * The date that this organization was founded.
     * @var string|null
     */
    public null|string $foundingDate ;

    /**
     * The place where the Organization was founded.
     * @var Place|string|null
     */
    public null|string|Place $foundingLocation ;

    /**
     * Indicates an OfferCatalog listing for this Organization, Person, or Service.
     * @var array|OfferCatalog|null
     */
    public null|array|OfferCatalog $hasOfferCatalog ;

    /**
     * Points-of-Sales operated by the organization or person.
     * @var array|Place|null
     */
    public null|array|Place $hasPOS ;

    /**
     * Specification of a shipping service offered by the organization.
     * @var array|object|null
     */
    public null|array|object $hasShippingService ;

    /**
     * Photographs of this organization (legacy spelling; see singular form, photo).
     */
    public ?array $images ;

    /**
     * The number of interactions for the CreativeWork using the WebSite or SoftwareApplication.
     * The most specific child type of InteractionCounter should be used.
     * @var InteractionCounter|array|null
     */
    public null|array|InteractionCounter $interactionStatistic ;

    /**
     * An organization identifier as defined in ISO 6523(-1). The identifier should be in the XXXX:YYYYYY:ZZZ or XXXX:YYYYYYformat.
     * @var string|null
     */
    public ?string $iso6523Code ;

    /**
     * Keywords or tags used to describe some item. Multiple textual entries in a keywords list are typically delimited by commas, or by repeating the property.
     * @var string|DefinedTerm|array|null
     */
    public null|string|DefinedTerm|array $keywords ;

    /**
     * The legal name of the organization
     */
    public ?string $legalName ;

    /**
     * Legal form of the organization.
     * @var string|object|null
     */
    public string|object|null $legalForm ;

    /**
     * An organization identifier that uniquely identifies a legal entity as defined in ISO 17442.
     * @var string|null
     */
    public ?string $leiCode ;

    /**
     * The location of, for example, where an event is happening, where an organization is located, or where an action takes place.
     * @var null|string|Place|PostalAddress|VirtualLocation
     */
    public null|string|Place|PostalAddress|VirtualLocation $location ;

    /**
     * An associated logo.
     */
    public null|string|ImageObject $logo ;

    /**
     * A pointer to products or services offered by the organization or person.
     * Inverse property: offeredBy
     * @var array|Offer|null
     */
    public null|array|Offer $makesOffer ;

    /**
     * A member of an Organization or a ProgramMembership. Organizations can be members of organizations; ProgramMembership is typically for individuals.
     * @var null|array|Person|Organization
     */
    public null|array|Person|Organization $member ;

    /**
     * An Organization (or ProgramMembership) to which this Person or Organization belongs.
     * @var null|array|Organization|MemberProgramTier|ProgramMembership
     */
    public null|array|Organization|MemberProgramTier|ProgramMembership $memberOf ;

    /**
     * The North American Industry Classification System (NAICS) code for a particular organization or business person.
     * @var string|null
     */
    public ?string $naics ;

    /**
     * Indicates the legal status of a non-profit organization in its primary place of business.
     * @var string|DefinedTerm|NonprofitType|null
     */
    public null|string|DefinedTerm|NonprofitType $nonprofitStatus ;

    /**
     * The number of employees in an organization, e.g. business.
     * @var QuantitativeValue|null
     */
    public ?QuantitativeValue $numberOfEmployees ;

    /**
     * Products owned by the organization or person.
     * @var array|Product|OwnershipInfo|null
     */
    public null|array|Product|OwnershipInfo $owns ;

    /**
     * The larger organization that this organization is a subOrganization of, if any.
     * Inverse property: subOrganization
     * @var array|Organization|null
     */
    public null|array|Organization $parentOrganization ;

    /**
     * Photographs of this organization (legacy spelling; see singular form, photo).
     */
    public ?array $photos ;

    /**
     * The providers of the organization
     */
    public ?array $providers ;

    /**
     * The publishingPrinciples property indicates (typically via URL) a document describing the editorial principles of an Organization (or individual, e.g. a Person writing a blog) that relate to their activities as a publisher, e.g. ethics or diversity policies.
     * @var string|CreativeWork
     */
    public string|CreativeWork $publishingPrinciples ;

    /**
     * A review of the item.
     * @var array|Review|null
     */
    public null|array|Review $review ;

    /**
     * A pointer to products or services sought by the organization or person (demand).
     * @var array|Demand|null
     */
    public null|array|Demand $seeks ;

    /**
     * The skills of the organization.
     * @var array|DefinedTerm|null
     */
    public null|array|DefinedTerm $skills ;

    /**
     * A slogan or motto associated with the item.
     * @var string|object|null
     */
    public object|string|null $slogan ;

    /**
     * A person or organization that supports a thing through a pledge, promise, or financial contribution. e.g. a sponsor of a Medical Study or a corporate sponsor of an event.
     */
    public array|Person|Organization|null $sponsor ;

    /**
     * A relationship between two organizations where the first includes the second, e.g., as a subsidiary.
     */
    public Organization|array|null $subOrganization ;

    /**
     * The Tax / Fiscal ID of the organization or person, e.g. the TIN in the US, the SIRET/SIREN in France or the CIF/NIF in Spain.
     * @var string|null
     */
    public null|string $taxID ;

    /**
     * The telephone number.
     * @var string|null|array|PropertyValue
     */
    public null|string|PropertyValue|array $telephone ;

    /**
     * The Value-added Tax ID of the organization or person.
     * @var string|null
     */
    public null|string $vatID ;
}


