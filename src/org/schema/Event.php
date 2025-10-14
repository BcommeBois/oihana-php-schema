<?php

namespace org\schema;

use org\schema\enumerations\events\EventAttendanceModeEnumeration;
use org\schema\enumerations\events\EventStatusType;
use org\schema\organizations\PerformingGroup;

/**
 * An event happening at a certain time and location, such as a concert, lecture, or festival. Repeated events may be structured as separate Event objects.
 */
class Event extends Thing
{
    /**
     * The subject matter of the content.
     */
    public string|object|null $about ;

    /**
     * An actor (individual or a group), e.g. in TV, radio, movie, video games etc., or in an event.
     * Actors can be associated with individual items or with a series, episode, clip.
     * @var array|Person|PerformingGroup|null
     */
    public null|array|Person|PerformingGroup $actor ;

    /**
     * The overall rating, based on a collection of reviews or ratings, of the item.
     * @var array|AggregateRating|null
     */
    public null|array|AggregateRating $aggregateRating ;

    /**
     * A person or organization attending the event.
     */
    public Person|Organization|array|null $attendee ;

    /**
     * An intended audience, i.e. a group for whom something was created. Supersedes serviceAudience.
     * @var array|Audience|null
     */
    public null|array|Audience $audience ;

    /**
     * The person or organization who wrote a composition, or who is the composer of a work performed at some event.
     */
    public Person|Organization|string|null $composer ;

    /**
     * A secondary contributor to the CreativeWork or Event.
     */
    public Person|Organization|string|null $contributor ;

    /**
     * A director of e.g. TV, radio, movie, video gaming etc. content, or of an event.
     * Directors can be associated with individual items or with a series, episode, clip.
     */
    public Person|array|null $director ;

    /**
     * The time admission will commence.
     * @var string|int|null
     */
    public null|string|int $doorTime ;

    /**
     * The duration of the item (movie, audio recording, event, etc.) in ISO 8601 duration format.
     * @var null|int|float|string|Duration|QuantitativeValue
     */
    public null|int|float|string|Duration|QuantitativeValue $duration ;

    /**
     * The end date and time of the event or item (in ISO 8601 date format).
     */
    public null|string|int $endDate ;

    /**
     * The eventAttendanceMode of an event indicates whether it occurs online, offline, or a mix.
     * @var EventAttendanceModeEnumeration|DefinedTerm|string|null
     */
    public null|EventAttendanceModeEnumeration|DefinedTerm|string $eventAttendanceMode ;

    /**
     * Associates an Event with a Schedule.
     * There are circumstances where it is preferable to share a schedule for a series of repeating events rather than data on the individual events themselves. For example, a website or application might prefer to publish a schedule for a weekly gym class rather than provide data on every event.
     * A schedule could be processed by applications to add forthcoming events to a calendar.
     * An Event that is associated with a Schedule using this property should not have startDate or endDate properties.
     * These are instead defined within the associated Schedule, this avoids any ambiguity for clients using the data.
     * The property might have repeated values to specify different schedules, e.g. for different months or seasons.
     * @var array|Schedule|null
     */
    public null|array|Schedule $eventSchedule ;

    /**
     * An eventStatus of an event represents its status; particularly useful when an event is cancelled or rescheduled.
     * @var string|DefinedTerm|EventStatusType|null
     */
    public string|DefinedTerm|EventStatusType|null $eventStatus ;

    /**
     * A person or organization that supports (sponsors) something through some kind of financial contribution.
     * @var array|Organization|Person|null
     */
    public null|array|Organization|Person $funder ;

    /**
     * The language of the content or performance or used in an action.
     * Please use one of the language codes from the IETF BCP 47 standard.
     * @var array|string|Language|null
     * @see IETF BCP 47 : http://tools.ietf.org/html/bcp47
     */
    public null|array|string|Language $inLanguage ;

    /**
     * A flag to signal that the item, event, or place is accessible for free.
     * @var bool|null
     */
    public null|bool $isAccessibleForFree;

    /**
     * Keywords or tags used to describe some item. Multiple textual entries in a keywords list are typically delimited by commas, or by repeating the property.
     * @var string|DefinedTerm|array|null
     */
    public null|string|DefinedTerm|array $keywords ;

    /**
     * The location of the event, organization or action.
     */
    public PostalAddress|Place|VirtualLocation|string|null $location ;

    /**
     * The total number of individuals that may attend an event or venue.
     * Supersedes capacity.
     * Supersedes $numAttendee.
     * @var int|null
     */
    public ?int $maximumAttendeeCapacity ;

    /**
     * The maximum physical attendee capacity of an Event whose eventAttendanceMode is OfflineEventAttendanceMode (or the offline aspects, in the case of a MixedEventAttendanceMode).
     * @var int|null
     */
    public ?int $maximumPhysicalAttendeeCapacity ;

    /**
     * The maximum virtual attendee capacity of an Event whose eventAttendanceMode is OnlineEventAttendanceMode (or the offline aspects, in the case of a MixedEventAttendanceMode).
     * @var int|null
     */
    public ?int $maximumVirtualAttendeeCapacity ;

    /**
     * A collection of Offer items to provide this item - for example, an offer to sell a product, rent the DVD of a movie, or give away tickets to an event.
     */
    public null|array|Offer|Demand $offers ;

    /**
     * An organizer of an Event.
     */
    public array|null|Person|Organization $organizer ;

    /**
     * Photographs of this place (legacy spelling; see singular form, photo).
     */
    public ?array $photos ;

    /**
     * The number of attendee places for an event that remain unallocated.
     */
    public ?int $remainingAttendeeCapacity ;

    /**
     * The remarks about the resource.
     */
    public string|object|null $remarks ;

    /**
     * A review of the item.
     * @var array|Review|null
     */
    public null|array|Review $review ;

    /**
     * The start date and time of the event or item (in ISO 8601 date format).
     */
    public null|string|int $startDate ;

    /**
     * An Event that is part of this event. For example, a conference event includes many presentations, each of which is a subEvent of the conference. Supersedes subEvents.
     */
    public Event|array|null $subEvent ;

    /**
     * A work featured in some event, e.g. exhibited in an ExhibitionEvent.
     */
    public string|array|CreativeWork|null $workFeatured ;

    /**
     * A work performed in some event, for example a play performed in a TheaterEvent.
     */
    public array|CreativeWork|null $workPerformed ;
}