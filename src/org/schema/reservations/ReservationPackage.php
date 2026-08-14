<?php

namespace org\schema\reservations;

use org\schema\Reservation;

/**
 * A group of multiple reservations with common values for all sub-reservations.
 */
class ReservationPackage extends Reservation
{
    /**
     * The individual reservations included in the package. Typically a repeated property.
     * @var array|Reservation|null
     */
    public null|array|Reservation $subReservation;
}