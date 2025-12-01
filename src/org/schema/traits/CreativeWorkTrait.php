<?php

namespace org\schema\traits;

use org\schema\AggregateRating;
use org\schema\AlignmentObject;
use org\schema\Audience;
use org\schema\CreativeWork;
use org\schema\creativeWork\Comment;
use org\schema\creativeWork\comments\CorrectionComment;
use org\schema\creativeWork\MediaObject;
use org\schema\creativeWork\medias\AudioObject;
use org\schema\creativeWork\WebPage;
use org\schema\DefinedTerm;
use org\schema\Demand;
use org\schema\enumerations\medias\IPTCDigitalSourceEnumeration;
use org\schema\Grant;
use org\schema\InteractionCounter;
use org\schema\ItemList;
use org\schema\Offer;
use org\schema\Organization;
use org\schema\Person;
use org\schema\Place;
use org\schema\places\Country;
use org\schema\Product;
use org\schema\Rating;
use org\schema\Review;

/**
 * The most generic kind of creative work, including books, movies, photographs, software programs, etc.
 * @see https://schema.org/CreativeWork
 */
trait CreativeWorkTrait
{
    /**
     * The subject matter of the content.
     */
    public string|object|null $about ;

    /**
     * An abstract is a short description that summarizes a CreativeWork.
     * @var string|null
     */
    public ?string $abstract ;

    /**
     * The human sensory perceptual system or cognitive faculty through which a person may process or perceive information.
     * @var string|null
     */
    public ?string $accessMode ;

    /**
     * A list of single or combined accessModes that are sufficient to understand all the intellectual content of a resource.
     * @var ItemList|null
     */
    public null|ItemList $accessModeSufficient;

    /**
     * Indicates that the resource is compatible with the referenced accessibility API.
     * @var string|null
     */
    public ?string $accessibilityAPI ;

    /**
     * Identifies input methods that are sufficient to fully control the described resource.
     * @var string|null
     */
    public ?string $accessibilityControl ;

    /**
     * Content features of the resource, such as accessible media, alternatives and supported enhancements for accessibility.
     * @var string|null
     */
    public ?string $accessibilityFeature ;

    /**
     * A characteristic of the described resource that is physiologically dangerous to some users. Related to WCAG 2.0 guideline 2.3.
     * @var string|null
     */
    public ?string $accessibilityHazard ;

    /**
     * A human-readable summary of specific accessibility features or deficiencies, consistent with the other accessibility metadata but expressing subtleties such as "short descriptions are present but long descriptions will be needed for non-visual users" or "short descriptions are present and no long descriptions are needed".
     * @var string|null
     */
    public ?string $accessibilitySummary ;

    /**
     * Specifies the Person that is legally accountable for the CreativeWork.
     * @var array|Person|null
     */
    public null|array|Person $accountablePerson ;

    /**
     * Indicates a page documenting how licenses can be purchased or otherwise acquired, for the current item.
     * @var string|CreativeWork|null
     */
    public null|string|CreativeWork $acquireLicensePage ;

    /**
     * The overall rating, based on a collection of reviews or ratings, of the item.
     * @var array|AggregateRating|null
     */
    public null|array|AggregateRating $aggregateRating ;

    /**
     * The alternative headline of this content.
     */
    public string|object|null $alternativeHeadline ;

    /**
     * Indicates a page or other link involved in archival of a CreativeWork. In the case of MediaReview,
     * the items in a MediaReviewItem may often become inaccessible,
     * but be archived by archival, journalistic, activist, or law enforcement organizations.
     * In such cases, the referenced page may not directly publish the content.
     */
    public string|WebPage|null $archivedAt ;

    /**
     * The item being described is intended to assess the competency or learning outcome defined by the referenced term.
     * @var string|DefinedTerm|null
     */
    public null|string|DefinedTerm $assesses ;

    /**
     * A media object that encodes this CreativeWork. This property is a synonym for encoding.
     * @var array|MediaObject|null
     */
    public null|array|MediaObject $associatedMedia ;

    /**
     * An intended audience, i.e. a group for whom something was created.
     */
    public null|array|Audience $audience ;

    /**
     * An embedded audio object.
     */
    public ?object $audio ;

    /**
     * The author of this content.
     */
    public null|string|AudioObject $author ;

    /**
     * An award won by or for this item.
     */
    public null|string|array $award ;

    /**
     * Fictional person connected with a creative work.
     */
    public null|array|Person $character ;

    /**
     * A citation or reference to another creative work, such as another publication, web page, scholarly article, etc.
     */
    public null|string|CreativeWork|array $citation ;

    /**
     * Comments, typically from users.
     * @var Comment|array|null
     */
    public null|Comment|array $comment ;

    /**
     * The number of comments this CreativeWork (e.g. Article, Question or Answer) has received.
     * This is most applicable to works published in Web sites with commenting system; additional comments may exist elsewhere.
     * @var int|null
     */
    public null|int $commentCount ;

    /**
     * Conditions that affect the availability of, or method(s) of access to, an item.
     *
     * Typically used for real world items such as an ArchiveComponent held by an ArchiveOrganization.
     * This property is not suitable for use as a general Web access control mechanism.
     * It is expressed only in natural language.
     *
     * For example "Available by appointment from the Reading Room" or "Accessible only from logged-in accounts ".
     */
    public null|string|object $conditionsOfAccess ;

    /**
     * The location depicted or described in the content. For example, the location in a photograph or painting.
     */
    public string|Place|null $contentLocation ;

    /**
     * Official rating of a piece of content—for example,'MPAA PG-13'.
     */
    public null|string|Rating|array $contentRating ;

    /**
     * The specific time described by a creative work, for works (e.g. articles, video objects etc.)
     * that emphasise a particular moment within an Event.
     */
    public string|null|int $contentReferenceTime ;

    /**
     * A secondary contributor to the CreativeWork or Event.
     */
    public null|Organization|Person|array $contributor ;

    /**
     * The party holding the legal copyright to the CreativeWork.
     */
    public null|Organization|Person|array $copyrightHolder ;

    /**
     * Text of a notice appropriate for describing the copyright aspects of this Creative Work,
     * ideally indicating the owner of the copyright for the Work.
     * @var string|object|null
     */
    public null|string|object $copyrightNotice ;

    /**
     * The year during which the claimed copyright for the CreativeWork was first asserted.
     * @var null|int|string
     */
    public null|int|string $copyrightYear ;

    /**
     * Indicates a correction to a CreativeWork, either via a CorrectionComment, textually or in another document.
     * @var null|CorrectionComment|string
     */
    public null|CorrectionComment|string $correction ;

    /**
     * The country of origin of something, including products as well as creative works such as movie and TV content.
     *
     * In the case of TV and movie, this would be the country of the principle offices of the production company
     * or individual responsible for the movie. For other kinds of CreativeWork it is difficult to provide
     * fully general guidance, and properties such as contentLocation and locationCreated may be more applicable.
     *
     * In the case of products, the country of origin of the product. The exact interpretation of this may vary
     * by context and product type, and cannot be fully enumerated here.
     *
     * @var Country|null
     */
    public null|Country $countryOfOrigin ;

    /**
     * The status of a creative work in terms of its stage in a lifecycle.
     *
     * Example terms include Incomplete, Draft, Published, Obsolete.
     *
     * Some organizations define a set of terms for the stages of their publication lifecycle.
     *
     * @var string|DefinedTerm|null
     */
    public null|string|DefinedTerm $creativeWorkStatus ;

    /**
     * The creator/author of this CreativeWork. This is the same as the Author property for CreativeWork.
     * @var Organization|Person|array|null
     */
    public null|Organization|Person|array $creator ;

    /**
     * Text that can be used to credit person(s) and/or organization(s) associated with a published Creative Work.
     * @var null|object|string
     */
    public null|object|string $creditText ;

    /**
     * The date on which the CreativeWork was created or the item was added to a DataFeed.
     */
    public ?string $dateCreated ;

    /**
     * The date on which the CreativeWork was most recently modified or when the item's entry was modified within a DataFeed.
     */
    public ?string $dateModified ;

    /**
     * Date of first broadcast/publication.
     */
    public ?string $datePublished ;

    /**
     * Indicates an IPTCDigitalSourceEnumeration code indicating the nature of the digital source(s) for some CreativeWork.
     */
    public null|IPTCDigitalSourceEnumeration $digitalSourceType ;

    /**
     * A link to the page containing the comments of the CreativeWork.
     * @var null|string
     */
    public null|string $discussionUrl ;

    /**
     * An EIDR (Entertainment Identifier Registry) identifier representing a specific
     * edit / edition for a work of film or television.
     *
     * For example, the motion picture known as "Ghostbusters" whose
     * titleEIDR is "10.5240/7EC7-228A-510A-053E-CBB8-J" has several edits,
     * e.g. "10.5240/1F2A-E1C5-680A-14C6-E76B-I" and "10.5240/8A35-3BEE-6497-5D12-9E4F-3".
     *
     * Since schema.org types like Movie and TVEpisode can be used for both works and their multiple expressions,
     * it is possible to use titleEIDR alone (for a general description), or alongside editEIDR
     * for a more edit-specific description.
     *
     * @var null|string
     */
    public null|string $editEIDR ;

    /**
     * Specifies the Person who edited the CreativeWork.
     */
    public ?string $editor ;

    /**
     * An alignment to an established educational framework.
     *
     * This property should not be used where the nature of the alignment can be described using a simple property,
     * for example to express that a resource teaches or assesses a competency.
     * @var AlignmentObject|null
     */
    public null|AlignmentObject $educationalAlignment;

    /**
     * The level in terms of progression through an educational or training context.
     *
     * Examples of educational levels include 'beginner', 'intermediate' or 'advanced', and formal sets of level indicators.
     *
     * @var null|string|array|DefinedTerm
     */
    public null|string|array|DefinedTerm $educationalLevel;

    /**
     * The purpose of a work in the context of education; for example, 'assignment', 'group work'.
     *
     * @var null|string|DefinedTerm
     */
    public null|string|DefinedTerm $educationalUse;

    /**
     * A media object that encodes this CreativeWork. This property is a synonym for associatedMedia.
     *
     * @var null|string|MediaObject
     */
    public null|string|MediaObject $encoding ;

    /**
     * Media type typically expressed using a MIME format (see IANA site and MDN reference)
     * e.g. application/zip for a SoftwareApplication binary, audio/mpeg for .mp3 etc.).
     *
     * In cases where a CreativeWork has several media type representations, encoding can be used to indicate
     * each MediaObject alongside particular encodingFormat information.
     *
     * Unregistered or niche encoding and file formats can be indicated instead
     * via the most appropriate URL, e.g. defining Web page or a Wikipedia/Wikidata entry.
     *
     * @var string|null
     */
    public ?string $encodingFormat ;

    /**
     * A creative work that this work is an example/instance/realization/derivation of.
     *
     * Inverse property: workExample
     *
     * @var array|CreativeWork|null
     */
    public null|array|CreativeWork $exampleOfWork ;

    /**
     * Date the content expires and is no longer useful or available.
     * For example a VideoObject or NewsArticle whose availability or relevance is time-limited,
     * or a ClaimReview fact check whose publisher wants to indicate that it may no longer be relevant
     * (or helpful to highlight) after some date.
     *
     * @var string|null
     */
    public ?string $expires ;

    /**
     * A person or organization that supports (sponsors) something through some kind of financial contribution.
     *
     * @var null|Organization|Person
     */
    public null|Organization|Person $funder ;

    /**
     * A Grant that directly or indirectly provide funding or sponsorship for this item.
     * See also ownershipFundingInfo.
     * @var null|Grant|array
     */
    public null|Grant|array $funding ;

    /**
     * Genre of the creative work, broadcast channel or group.
     * @var null|string|DefinedTerm
     */
    public null|string|DefinedTerm $genre ;

    /**
     * The headline of this content.
     */
    public string|object|null $headline ;

    /**
     * The language of the content or performance or used in an action.
     */
    public array|string|null $inLanguage ;

    /**
     * The number of interactions for the CreativeWork using the WebSite or SoftwareApplication.
     * The most specific child type of InteractionCounter should be used.
     * @var InteractionCounter|array|null
     */
    public null|array|InteractionCounter $interactionStatistic ;

    /**
     * A flag to signal that the item, event, or place is accessible for free.
     */
    public ?bool $isAccessibleForFree ;

    /**
     * A resource that was used in the creation of this resource. This term can be repeated for multiple sources.
     */
    public array|object|null $isBasedOn ;

    /**
     * Keywords or tags used to describe some item. Multiple textual entries in a keywords list are typically delimited by commas, or by repeating the property.
     * @var string|DefinedTerm|array|null
     */
    public null|string|DefinedTerm|array $keywords ;
    
    /**
     * The location where the CreativeWork was created, which may not be the same as the location depicted in the CreativeWork.
     */
    public string|object|null $locationCreated ;

    /**
     * Indicates the primary entity described in some page or other CreativeWork.
     */
    public string|object|null $mainEntity ;

    /**
     * A material that something is made from, e.g. leather, wool, cotton, paper.
     * @var array|Product|string|null
     */
    public array|Product|string|null $material ;

    /**
     * The mentions of this content.
     */
    public array|string|null $mentions ;

    /**
     * An offer to provide this item.
     * @var array|Offer|null|Demand
     */
    public array|Offer|Demand|null $offers ;

    /**
     * The position of an item in a series or sequence of items.
     */
    public null|int|string $position ;

    /**
     * The person or organization who produced the work.
     */
    public ?object $producer ;

    /**
     * The service provider, service operator, or service performer; the goods producer. Another party (a seller) may offer those services or goods on behalf of the provider. A provider may also serve as the seller.
     */
    public string|object|null $provider ;

    /**
     * The Event where the CreativeWork was recorded. The CreativeWork may capture all or part of the event.
     */
    public ?object $recordedAt ;

    /**
     * The place and time the release was issued, expressed as a PublicationEvent.
     */
    public ?object $releaseEvent ;

    /**
     * A review of the item.
     * @var array|Review|null
     */
    public null|array|Review $review ;

    /**
     * The Organization on whose behalf the creator was working.
     */
    public ?object $sourceOrganization ;

    /**
     * A person or organization that supports a thing through a pledge, promise, or financial contribution. e.g. a sponsor of a Medical Study or a corporate sponsor of an event.
     */
    public array|object|string|null $sponsor ;

    /**
     * The "temporal" property can be used in cases where more specific properties (e.g. temporalCoverage, dateCreated, dateModified, datePublished) are not known to be appropriate.
     */
    public string|object|null $temporal ;

    /**
     * The temporalCoverage of a CreativeWork indicates the period that the content applies to, i.e. that it describes, either as a Date Time or as a textual string indicating a time period in ISO 8601 time interval format. In the case of a Dataset it will typically indicate the relevant time period in a precise notation (e.g. for a 2011 census dataset, the year 2011 would be written "2011/2012"). Other forms of content e.g. ScholarlyArticle, Book, TVSeries or TVEpisode may indicate their temporalCoverage in broader terms - textually or via well-known URL. Written works such as books may sometimes have precise temporal coverage too, e.g. a work set in 1939 - 1945 can be indicated in ISO 8601 interval format format via "1939/1945".
     * Open-ended date ranges can be written with ".." in place of the end date. For example, "2015-11/.." indicates a range beginning in November 2015 and with no specified final date. This is tentative and might be updated in future when ISO 8601 is officially updated. Supersedes datasetTimeInterval.
     */
    public string|object|null $temporalCoverage ;

    /**
     * A thumbnail image relevant to the Thing.
     */
    public string|object|null $thumbnailUrl ;

    /**
     * The text of the creative work.
     */
    public string|object|null $text ;

    /**
     * Approximate or typical time it takes to work with or through this learning resource for the typical intended target audience, e.g. 'PT30M', 'PT1H25M'.
     */
    public ?string $timeRequired ;

    /**
     * Organization or person who adapts a creative work to different languages, regional differences and technical requirements of a target market, or that translates during some event.
     */
    public string|object|null $translator ;

    /**
     * The typical expected age range, e.g. '7-9', '11-'.
     */
    public string|object|null $typicalAgeRange ;

    /**
     * The schema.org usageInfo property indicates further information about a CreativeWork.
     * This property is applicable both to works that are freely available and to those that require payment or other transactions.
     * It can reference additional information, e.g. community expectations on preferred linking and citation conventions, as well as purchasing details.
     * For something that can be commercially licensed, usageInfo can provide detailed, resource-specific information about licensing options.
     * This property can be used alongside the license property which indicates license(s) applicable to some piece of content. The usageInfo property can provide information about other licensing options, e.g. acquiring commercial usage rights for an image that is also available under non-commercial creative commons licenses.
     */
    public string|CreativeWork|null $usageInfo ;

    /**
     * The version of the CreativeWork embodied by a specified resource.
     */
    public null|string|int $version ;

    /**
     * An embedded video object.
     */
    public string|object|null $video ;
}


