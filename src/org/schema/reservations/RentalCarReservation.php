<?php

namespace org\schema\reservations;

use org\schema\Place;
use org\schema\QuantitativeValue;
use org\schema\Reservation;

/**
 * A reservation for a rental car.
 *
 * Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations.
 * For offers of tickets, use Offer.
 */
class RentalCarReservation extends Reservation
{
    /**
     * Where a rental car can be dropped off.
     * @var array|Place|null
     */
    public null|array|Place $dropoffLocation ;

    /**
     * When a rental car can be dropped off.
     * @var string|int|null
     */
    public null|string|int $dropoffTime ;

    /**
     * Where a taxi will pick up a passenger or a rental car can be picked up.
     * @var array|Place|null
     */
    public null|array|Place $pickupLocation ;

    /**
     * When a taxi will pick up a passenger or a rental car can be picked up.
     */
    public null|string|int $pickupTime ;
}