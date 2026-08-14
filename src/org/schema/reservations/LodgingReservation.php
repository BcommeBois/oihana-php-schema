<?php

namespace org\schema\reservations;

use org\schema\QuantitativeValue;
use org\schema\Reservation;

/**
 * A reservation for lodging at a hotel, motel, inn, etc.
 *
 * Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations.
 * For offers of tickets, use Offer.
 */
class LodgingReservation extends Reservation
{
    /**
     * The earliest someone may check into a lodging establishment.
     * @var string|int|null
     */
    public null|string|int $checkinTime ;

    /**
     * The latest someone may check out of a lodging establishment.
     * @var string|int|null
     */
    public null|string|int $checkoutTime ;

    /**
     * A full description of the lodging unit.
     * @var null|string
     */
    public null|string $lodgingUnitDescription ;

    /**
     * Textual description of the unit type (including suite vs. room, size of bed, etc.).
     * @var string|array|QuantitativeValue|null
     */
    public null|string|array|QuantitativeValue $lodgingUnitType ;

    /**
     * The number of adults staying in the unit.
     * @var null|int
     */
    public null|int $numAdults ;

    /**
     * The number of children staying in the unit.
     * @var null|int
     */
    public null|int $numChildren ;
}