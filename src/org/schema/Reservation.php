<?php

namespace org\schema;

use org\schema\enumerations\ReservationStatusType;

/**
 * Describes a reservation for travel, dining or an event.
 * Some reservations require tickets.
 *
 * Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations.
 * For offers of tickets, restaurant reservations, flights, or rental cars, use Offer.
 */
class Reservation extends Intangible
{
    /**
     * The date and time the reservation was booked.
     * @var string|int|null
     */
    public null|string|int $bookingTime ;

    /**
     * An entity that arranges for an exchange between a buyer and a seller.
     * In most cases a broker never acquires or releases ownership of a product or service involved in an exchange.
     * If it is not clear whether an entity is a broker, seller, or buyer, the latter two terms are preferred.
     * @var array|Organization|Person|null
     */
    public null|array|Organization|Person $broker ;

    /**
     * The date and time the reservation was modified.
     * @var string|int|null
     */
    public null|string|int $modifiedTime ;

    /**
     * The currency of the price, or a price component when attached to PriceSpecification and its subtypes.
     * @var string|null
     */
    public null|string $priceCurrency ;

    /**
     * Any membership in a frequent flyer, hotel loyalty program, etc. being applied to the reservation.
     * @var array|ProgramMembership|null
     */
    public null|array|ProgramMembership $programMembershipUsed ;

    /**
     * The service provider, service operator, or service performer; the goods producer. Another party (a seller) may offer those services or goods on behalf of the provider.
     * A provider may also serve as the seller.
     * @var array|Organization|Person|null
     */
    public null|array|Organization|Person $provider ;

    /**
     * The thing -- flight, event, restaurant, etc. being reserved.
     * @var array|Thing|null
     */
    public null|array|Thing $reservationFor ;

    /**
     * A unique identifier for the reservation.
     * @var string|int|null
     */
    public null|string|int $reservationId ;

    /**
     * The current status of the reservation.
     * @var string|null|ReservationStatusType
     */
    public null|string|ReservationStatusType $reservationStatus ;

    /**
     * A ticket associated with the reservation.
     * @var array|Ticket|null
     */
    public null|array|Ticket $reservedTicket ;

    /**
     * The total price for the reservation or ticket, including applicable taxes, shipping, etc.
     * Usage guidelines:
     * Use values from 0123456789 (Unicode 'DIGIT ZERO' (U+0030) to 'DIGIT NINE' (U+0039)) rather than superficially similar Unicode symbols.
     * Use '.' (Unicode 'FULL STOP' (U+002E)) rather than ',' to indicate a decimal point. Avoid using these symbols as a readability separator.
     * @var null|array|float|int|PriceSpecification|string
     */
    public null|array|float|int|PriceSpecification|string $totalPrice ;

    /**
     * The person or organization the reservation or ticket is for.
     * @var array|Organization|Person|null
     */
    public null|array|Organization|Person $underName ;
}