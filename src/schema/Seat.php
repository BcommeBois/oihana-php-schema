<?php

namespace org\schema;

/**
 * Used to describe a seat, such as a reserved seat in an event reservation.
 * @see https://schema.org/Seat
 */
class Seat extends Intangible
{
    /**
     * The location of the reserved seat (e.g., 27).
     * @var string|null
     */
    public ?string $seatNumber ;

    /**
     * The row location of the reserved seat (e.g., B).
     * @var string|null
     */
    public ?string $seatRow ;

    /**
     * The section location of the reserved seat (e.g. Orchestra).
     * @var string|null
     */
    public ?string $seatSection ;

    /**
     * The type/class of the seat.
     * @var string|null|QualitativeValue
     */
    public null|string|QualitativeValue $seatType ;

}