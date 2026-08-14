<?php

namespace org\schema\enumerations;

/**
 * Enumerated status values for Reservation.
 *
 * The following legacy values should be accepted:
 * - https://schema.org/ReservationCancelled
 * - https://schema.org/ReservationConfirmed
 * - https://schema.org/ReservationHold
 */
class ReservationStatusType extends StatusEnumeration
{
    /**
     * The status for a previously confirmed reservation that is now cancelled.
     */
    public const string RESERVATION_CANCELLED = 'https://schema.org/ReservationCancelled' ;

    /**
     * The status of a confirmed reservation.
     */
    public const string RESERVATION_CONFIRMED = 'https://schema.org/ReservationConfirmed' ;

    /**
     * The status of a reservation on hold pending an update like credit card number or flight changes.
     */
    public const string RESERVATION_HOLD = 'https://schema.org/ReservationHold' ;

    /**
     * The status of a reservation when a request has been sent, but not confirmed.
     */
    public const string RESERVATION_PENDING = 'https://schema.org/ReservationPending' ;
}