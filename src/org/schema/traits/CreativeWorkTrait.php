<?php

namespace org\schema\traits;

use org\schema\AggregateRating;
use org\schema\Audience;
use org\schema\CreativeWork;
use org\schema\creativeWork\MediaObject;
use org\schema\creativeWork\medias\AudioObject;
use org\schema\creativeWork\WebPage;
use org\schema\DefinedTerm;
use org\schema\Demand;
use org\schema\InteractionCounter;
use org\schema\ItemList;
use org\schema\Offer;
use org\schema\Person;
use org\schema\Place;
use org\schema\Product;
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
     * The location depicted or described in the content. For example, the location in a photograph or painting.
     */
    public string|Place|null $contentLocation ;

    /**
     * Official rating of a piece of content—for example,'MPAA PG-13'.
     */
    public string $contentRating ;

    /**
     * A secondary contributor to the CreativeWork or Event.
     */
    public string|object|null $contributor ;

    /**
     * The party holding the legal copyright to the CreativeWork.
     */
    public ?string $copyrightHolder ;

    /**
     * The year during which the claimed copyright for the CreativeWork was first asserted.
     */
    public ?string $copyrightYear ;

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
     * Specifies the Person who edited the CreativeWork.
     */
    public ?string $editor ;

    /**
     * The encoding of this content.
     */
    public ?string $encoding ;

    /**
     * Media type typically expressed using a MIME format (see IANA site and MDN reference) e.g. application/zip for a SoftwareApplication binary, audio/mpeg for .mp3 etc.).
     */
    public ?string $encodingFormat ;

    /**
     * Date the content expires and is no longer useful or available. For example a VideoObject or NewsArticle whose availability or relevance is time-limited, or a ClaimReview fact check whose publisher wants to indicate that it may no longer be relevant (or helpful to highlight) after some date.
     */
    public ?string $expires ;

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
    public ?int $position ;

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
     * The temporalCoverage of a CreativeWork indicates the period that the content applies to, i.e. that it describes, either as a DateTime or as a textual string indicating a time period in ISO 8601 time interval format. In the case of a Dataset it will typically indicate the relevant time period in a precise notation (e.g. for a 2011 census dataset, the year 2011 would be written "2011/2012"). Other forms of content e.g. ScholarlyArticle, Book, TVSeries or TVEpisode may indicate their temporalCoverage in broader terms - textually or via well-known URL. Written works such as books may sometimes have precise temporal coverage too, e.g. a work set in 1939 - 1945 can be indicated in ISO 8601 interval format format via "1939/1945".
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


