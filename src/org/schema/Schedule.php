<?php

namespace org\schema;

use DateTime;
use org\schema\enumerations\DayOfWeek;

/**
 * A schedule defines a repeating time period used to describe a regularly occurring Event.
 * At a minimum a schedule will specify repeatFrequency which describes the interval between occurrences of the event.
 * Additional information can be provided to specify the schedule more precisely.
 * This includes identifying the day(s) of the week or month when the recurring event will take place, in addition to its start and end time.
 * Schedules may also have start and end dates to indicate when they are active, e.g. to define a limited calendar of events.
 * @see https://schema.org/Schedule
 */
class Schedule extends Intangible
{
    /**
     * Defines the day(s) of the week on which a recurring Event takes place.
     * May be specified using either DayOfWeek, or alternatively Text conforming to iCal's syntax for byDay recurrence rules.
     * @var DayOfWeek|DefinedTerm|string|array|null
     */
    public null|DayOfWeek|DefinedTerm|string|array $byDay ;

    /**
     * Defines the month(s) of the year on which a recurring Event takes place.
     * Specified as an Integer between 1-12. January is 1.
     * @var int|array|null
     */
    public null|int|array $byMonth ;

    /**
     * Defines the day(s) of the month on which a recurring Event takes place.
     * Specified as an Integer between 1-31.
     * @var int|array|null
     */
    public null|int|array $byMonthDay;

    /**
     * Defines the week(s) of the month on which a recurring Event takes place.
     * Specified as an Integer between 1-5. For clarity, byMonthWeek is best used in conjunction with byDay to indicate concepts like the first and third Mondays of a month.
     * @var int|array|null
     */
    public null|int|array $byMonthWeek;

    /**
     * The duration of the item (movie, audio recording, event, etc.) in ISO 8601 duration format.
     * @var null|int|float|string|Duration|QuantitativeValue
     */
    public null|int|float|string|Duration|QuantitativeValue $duration ;

    /**
     * The end date and time of the event or item (in ISO 8601 date format).
     */
    public null|string|int|DateTime $endDate ;

    /**
     * The endTime of something.
     * For a reserved event or service (e.g. FoodEstablishmentReservation), the time that it is expected to end.
     * For actions that span a period of time, when the action was performed. E.g. John wrote a book from January to December.
     * For media, including audio and video, it's the time offset of the end of a clip within a larger file.
     * Note that Event uses startDate/endDate instead of startTime/endTime, even when describing dates with times.
     * This situation may be clarified in future revisions.
     */
    public null|string|int|DateTime $endTime ;

    /**
     * Defines a Date or DateTime during which a scheduled Event will not take place. The property allows exceptions to a Schedule to be specified. If an exception is specified as a DateTime then only the event that would have started at that specific date and time should be excluded from the schedule. If an exception is specified as a Date then any event that is scheduled for that 24 hour period should be excluded from the schedule.
     * This allows a whole day to be excluded from the schedule without having to itemise every scheduled event.
     * @var string|array|int|DateTime|null
     */
    public null|array|string|int|DateTime $exceptDate ;

    /**
     * Defines the number of times a recurring Event will take place.
     * @var int|null
     */
    public null|int $repeatCount ;

    /**
     * Defines the frequency at which Events will occur according to a schedule Schedule. The intervals between events should be defined as a Duration of time.
     * @var Duration|string|int|float|null
     */
    public null|Duration|string|int|float $repeatFrequency ;

    /**
     * Indicates the timezone for which the time(s) indicated in the Schedule are given. The value provided should be among those listed in the IANA Time Zone Database.
     * @var string|null
     */
    public ?string $scheduleTimezone ;

    /**
     * The start date and time of the event or item (in ISO 8601 date format).
     */
    public null|string|int|DateTime $startDate ;

    /**
     * The startTime of something. For a reserved event or service (e.g. FoodEstablishmentReservation), the time that it is expected to start. For actions that span a period of time, when the action was performed. E.g. John wrote a book from January to December. For media, including audio and video, it's the time offset of the start of a clip within a larger file.
     */
    public null|string|int|DateTime $startTime ;
}