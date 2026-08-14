<?php

namespace org\schema\reservations;

use org\schema\QuantitativeValue;
use org\schema\Reservation;

/**
 * A reservation for air travel.
 *
 * Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations.
 * For offers of tickets, use Offer.
 */
class FlightReservation extends Reservation
{
    /**
     * The airline-specific indicator of boarding order / preference.
     * @var null|string
     */
    public null|string $boardingGroup ;

    /**
     * The priority status assigned to a passenger for security or boarding (e.g. FastTrack or Priority).
     * @var string|array|QuantitativeValue|null
     */
    public null|string|array|QuantitativeValue $passengerPriorityStatus ;

    /**
     * The passenger's sequence number as assigned by the airline.
     * @var null|string
     */
    public null|string $passengerSequenceNumber ;

    /**
     * The type of security screening the passenger is subject to.
     * @var null|string
     */
    public null|string $securityScreening ;
}