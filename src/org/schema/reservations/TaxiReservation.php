<?php

namespace org\schema\reservations;

use org\schema\Place;
use org\schema\QuantitativeValue;
use org\schema\Reservation;

/**
 * A reservation for a taxi.
 *
 * Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations.
 * For offers of tickets, use Offer.
 */
class TaxiReservation extends Reservation
{
    /**
     * Number of people the reservation should accommodate.
     * @var int|array|QuantitativeValue|null
     */
    public null|int|array|QuantitativeValue $partySize ;

    /**
     * Where a taxi will pick up a passenger or a rental car can be picked up.
     * @var array|Place|null
     */
    public null|array|Place $pickupLocation ;

    /**
     * When a taxi will pick up a passenger or a rental car can be picked up.
     * @var string|int|null
     */
    public null|string|int $pickupTime ;
}