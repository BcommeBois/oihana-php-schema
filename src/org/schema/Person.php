<?php

namespace org\schema;

use DateTime;
use org\schema\creativeWork\Certification;
use org\schema\creativeWork\medias\ImageObject;
use org\schema\organizations\EducationalOrganization;
use org\schema\places\Country;

/**
 * A person (alive, dead, undead, or fictional).
 */
class Person extends Thing
{
    /**
     * An additional name for a Person, can be used for a middle name.
     */
    public array|string|null $additionalName = null ;

    /**
     * Physical address of the item (PostalAddress or any object to describe it).
     */
    public PostalAddress|string|array|null $address = null ;

    /**
     * An organization that this person is affiliated with. For example, a school/university, a club, or a team.
     * @var null|array|Organization
     */
    public null|array|Organization $affiliation ;

    /**
     * The number of completed interactions for this entity, in a particular role (the 'agent'), in a particular action (indicated in the statistic), and in a particular context (i.e. interactionService).
     * @var InteractionCounter|array|null
     */
    public null|array|InteractionCounter $agentInteractionStatistic ;

    /**
     * An organization that the person is an alumni of.
     * @var null|array|Organization|EducationalOrganization
     */
    public null|array|Organization|EducationalOrganization $alumniOf ;

    /**
     * An award won by or for this item.
     */
    public null|array|string $award ;

    /**
     * The Date of birth.
     */
    public string|null|DateTime $birthDate ;

    /**
     * The place where the person was born.
     */
    public Place|string|null $birthPlace ;

    /**
     * The brand(s) associated with a product or service, or the brand(s) maintained by an organization or business person.
     * @var null|Brand|Organization
     */
    public null|Brand|Organization $brand ;

    /**
     * The children of the person.
     * @var array|Person|null
     */
    public null|array|Person $children ;

    /**
     * The colleague of the person.
     * @var array|Person|null
     */
    public null|array|Person $colleague ;

    /**
     * A contact point for a person or organization.
     * @var array|ContactPoint|null
     */
    public null|array|ContactPoint $contactPoint ;

    /**
     * The Date of death.
     */
    public string|null|DateTime $deathDate ;

    /**
     * The place where the person died.
     */
    public Place|string|null $deathPlace ;

    /**
     * The Dun & Bradstreet DUNS number for identifying an organization or business person.
     */
    public ?string $duns ;

    /**
     * The email of the user.
     */
    public ?string $email ;

    /**
     * The family name of the user.
     */
    public ?string $familyName ;

    /**
     * The fax number.
     * @var string|null|array|PropertyValue
     */
    public null|string|PropertyValue|array $faxNumber ;

    /**
     * The most generic uni-directional social relation.
     * @var array|Person|null
     */
    public null|array|Person $follows ;

    /**
     * A person or organization that supports (sponsors) something through some kind of financial contribution.
     * @var null|string|array|Person|Organization
     */
    public null|string|array|Person|Organization $funder ;

    /**
     * The gender of the user.
     */
    public Enumeration|DefinedTerm|string|null $gender ;

    /**
     * The given name of the user (first name).
     */
    public ?string $givenName ;

    /**
     * The Global Location Number (GLN, sometimes also referred to as International Location Number or ILN) of the respective organization, person, or place.
     * The GLN is a 13-digit number used to identify parties and physical locations.
     * @var string|null
     */
    public ?string $globalLocationNumber ;

    /**
     * Certification information about a product, organization, service, place, or person.
     * @var array|Certification|null
     */
    public null|array|Certification $hasCertification ;

    /**
     * The Person's occupation. For past professions, use Role for expressing dates.
     * @var array|string|null
     */
    public null|array|string $hasOccupation ; // TODO https://schema.org/Occupation

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
     * The height of the item.
     * @var Distance|QuantitativeValue|null
     */
    public null|Distance|QuantitativeValue $height ;

    /**
     * A contact location for a person's residence.
     * @var null|string|array|ContactPoint|Place
     */
    public null|string|array|ContactPoint|Place $homeLocation ;

    /**
     * An honorific prefix preceding a Person's name such as Dr/Mrs/Mr.
     * @var string|null
     */
    public ?string $honorificPrefix ;

    /**
     * An honorific suffix following a Person's name such as M.D./PhD/MSCSW.
     * @var string|null
     */
    public ?string $honorificSuffix ;

    /**
     * The number of interactions for the CreativeWork using the WebSite or SoftwareApplication.
     * The most specific child type of InteractionCounter should be used.
     * @var InteractionCounter|array|null
     */
    public null|array|InteractionCounter $interactionStatistic ;

    /**
     * The International Standard of Industrial Classification of All Economic Activities (ISIC), Revision 4 code for a particular organization, business person, or place.
     * @var string|null
     */
    public ?string $isicV4 ;

    /**
     * The job(s) title(s) of the person (for example, Painter, etc.).
     */
    public array|string|null $jobTitle ;

    /**
     * The most generic bi-directional social/work relation.
     * @var array|Person|null
     */
    public null|array|Person $knows ;

    /**
     * Of a Person, and less typically of an Organization, to indicate a topic that is known about - suggesting possible expertise but not implying it. We do not distinguish skill levels here, or relate this to educational content, events, objectives or JobPosting descriptions.
     * @var string|Thing|null
     */
    public null|string|Thing $knowsAbout ;

    /**
     * The most generic bi-directional social/work relation.
     * @var string|array|Language|null
     */
    public null|array|Language|string $knowsLanguage ;

    /**
     * Keywords or tags used to describe some item. Multiple textual entries in a keywords list are typically delimited by commas, or by repeating the property.
     * @var string|DefinedTerm|array|null
     */
    public null|string|DefinedTerm|array $keywords ;

    /**
     * The location of the organization
     */
    public null|string|ImageObject $logo ;

    /**
     * A pointer to products or services offered by the organization or person.
     * Inverse property: offeredBy
     * @var array|Offer|null
     */
    public null|array|Offer $makesOffer ;

    /**
     * An Organization to which this Person or Organization belongs.
     */
    public array|object|null $memberOf ;

    /**
     * The North American Industry Classification System (NAICS) code for a particular organization or business person.
     * @var string|null
     */
    public ?string $naics ;

    /**
     * Nationality of the person.
     */
    public null|string|Country $nationality ;

    /**
     * The total financial value of the person as calculated by subtracting the total value of liabilities from the total value of assets.
     * @var PriceSpecification|MonetaryAmount|null
     */
    public null|PriceSpecification|MonetaryAmount $netWorth ;

    /**
     * Products owned by the organization or person.
     * @var array|Product|OwnershipInfo|null
     */
    public null|array|Product|OwnershipInfo $owns ;

    /**
     * A parent of this person.
     * @var array|Person|null
     */
    public null|array|Person $parent ;

    /**
     * Event that this person is a performer or participant in.
     * @var array|Event|null
     */
    public null|array|Event $performerIn ;

    /**
     * Photographs of this person (legacy spelling; see singular form, photo).
     */
    public ?array $photos ;

    /**
     * The publishingPrinciples property indicates (typically via URL) a document describing the editorial principles of an Organization (or individual, e.g. a Person writing a blog) that relate to their activities as a publisher, e.g. ethics or diversity policies. When applied to a CreativeWork (e.g. NewsArticle) the principles are those of the party primarily responsible for the creation of the CreativeWork.
     * @var array|CreativeWork|string|null
     */
    public null|array|CreativeWork|string $publishingPrinciples ;

    /**
     * The most generic familial relation.
     * @var array|Person|null
     */
    public null|array|Person $relatedTo ;

    /**
     * The remarks about the resource.
     */
    public ?array $remarks ;

    /**
     * A pointer to products or services sought by the organization or person (demand).
     * @var array|Demand|null
     */
    public null|array|Demand $seeks ;

    /**
     * A sibling of the person.
     * @var array|Person|null
     */
    public null|array|Person $sibling ;

    /**
     * A statement of knowledge, skill, ability, task or any other assertion expressing a competency that is either claimed by a person, an organization or desired or required to fulfill a role or to work in an occupation.
     * @var array|DefinedTerm|string|null
     */
    public null|array|DefinedTerm|string $skills ;

    /**
     * A person or organization that supports a thing through a pledge, promise, or financial contribution.
     */
    public null|array|Organization|Person $sponsor ;

    /**
     * The person's spouse.
     * @var array|Person|null
     */
    public null|array|Person $spouse ;

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

    /**
     * The weight of the product or person.
     * @var null|QuantitativeValue|Mass
     */
    public null|QuantitativeValue|Mass $weight ;

    /**
     * A contact location for a person's place of work.
     * @var null|string|array|ContactPoint|Place
     */
    public null|string|array|ContactPoint|Place $workLocation ;

    /**
     * Organizations that the person works for.
     * @var null|string|array|Organization
     */
    public null|string|array|Organization $workFor ;
}


