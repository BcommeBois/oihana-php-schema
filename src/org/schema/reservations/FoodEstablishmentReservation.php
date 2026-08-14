<?php

namespace org\schema\reservations;

use org\schema\QuantitativeValue;
use org\schema\Reservation;

/**
 * A reservation to dine at a food-related business.
 *
 * Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations.
 * For offers of tickets, use Offer.
 */
class FoodEstablishmentReservation extends Reservation
{
    /**
     * The endTime of something.
     * For a reserved event or service (e.g. FoodEstablishmentReservation), the time that it is expected to end.
     * For actions that span a period of time, when the action was performed. E.g. John wrote a book from January to December.
     * For media, including audio and video, it's the time offset of the end of a clip within a larger file.
     * Note that Event uses startDate/endDate instead of startTime/endTime, even when describing dates with times.
     * This situation may be clarified in future revisions.
     */
    public null|string|int $endTime ;

    /**
     * Number of people the reservation should accommodate.
     * @var int|array|QuantitativeValue|null
     */
    public null|int|array|QuantitativeValue $partySize ;

    /**
     * The startTime of something. For a reserved event or service (e.g. FoodEstablishmentReservation), the time that it is expected to start. For actions that span a period of time, when the action was performed. E.g. John wrote a book from January to December. For media, including audio and video, it's the time offset of the start of a clip within a larger file.
     */
    public null|string|int $startTime ;
}