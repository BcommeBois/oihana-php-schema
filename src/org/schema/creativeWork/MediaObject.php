<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;
use org\schema\GeoShape;
use org\schema\MediaSubscription;
use org\schema\Organization;
use org\schema\Place;

/**
 * An media object file representation.
 */
class MediaObject extends CreativeWork
{
    /**
     * A NewsArticle associated with the Media Object.
     * @var CreativeWork|null
     */
    public ?CreativeWork $associatedArticle ; // TODO NewsArticle

    /**
     * The bitrate of the item.
     */
    public ?float $bitrate ;

    /**
     * The contentSize of the item.
     */
    public ?float $contentSize ;

    /**
     * The contentUrl of the item.
     */
    public ?string $contentUrl ;

    /**
     * The duration of the item.
     */
    public ?int $duration ;

    /**
     * The embedUrl of the item.
     */
    public ?string $embedUrl ;

    /**
     * The CreativeWork encoded by this media object.
     * @var CreativeWork|null
     */
    public ?CreativeWork $encodesCreativeWork ;

    /**
     * The endTime of something. For a reserved event or service (e.g. FoodEstablishmentReservation), the time that it is expected to end. For actions that span a period of time, when the action was performed. E.g. John wrote a book from January to December. For media, including audio and video, it's the time offset of the end of a clip within a larger file.
     * Note that Event uses startDate/endDate instead of startTime/endTime, even when describing dates with times. This situation may be clarified in future revisions.
     * @var string|int|null
     */
    public null|string|int $endTime ;

    /**
     * The height of the item.
     */
    public ?float $height ;

    /**
     * The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place,
     * r the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is not valid,
     * e.g. a region where the transaction is not allowed.
     * @var GeoShape|Place|string|null
     */
    public GeoShape|Place|string|null $ineligibleRegion ;

    /**
     * Used to indicate a specific claim contained, implied, translated or refined from the content of a MediaObject or other CreativeWork. The interpreting party can be indicated using claimInterpreter.
     * @var Claim|null
     */
    public ?Claim $interpretedAsClaim ;

    /**
     * The playerType of the item.
     */
    public ?string $playerType ;

    /**
     * When multiple image appear in an entry, indicates which is primary. At most one image may be primary. Default value is false.
     */
    public ?bool $primary ;

    /**
     * The production company or studio responsible for the item, e.g. series, video game, episode etc.
     * @var array|Organization|null
     */
    public null|array|Organization $productionCompany ;

    /**
     * The regions where the media is allowed.
     * If not specified, then it's assumed to be allowed everywhere.
     * Specify the countries in ISO 3166 format.
     * @var array|Place|string|null
     */
    public null|array|string|Place $regionsAllowed ;

    /**
     * Indicates if use of the media require a subscription (either paid or free).
     * Allowed values are true or false (note that an earlier version had 'yes', 'no').
     * @var bool|MediaSubscription|null
     */
    public null|bool|MediaSubscription $requiresSubscription ;

    /**
     * The SHA-2 SHA256 hash of the content of the item. For example,
     * a zero-length input has value 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'.
     * @var string|null
     */
    public ?string $sha256 ;

    /**
     * The startTime of something.
     * For a reserved event or service (e.g. FoodEstablishmentReservation), the time that it is expected to start.
     * For actions that span a period of time, when the action was performed.
     * E.g. John wrote a book from January to December.
     * For media, including audio and video, it's the time offset of the start of a clip within a larger file.
     * Note that Event uses startDate/endDate instead of startTime/endTime, even when describing dates with times. This situation may be clarified in future revisions.
     * @var string|int|null
     */
    public null|string|int $startTime ;

    /**
     * Date (including time if available) when this media object was uploaded to this site.
     * @var string|null|int
     */
    public null|string|int $uploadDate ;

    /**
     * The width of the item.
     */
    public ?float $width ;
}


