<?php

namespace org\schema;

use DateTime;

/**
 * Used to describe a ticket to an event, a flight, a bus ride, etc.
 * @see https://schema.org/Ticket
 */
class Ticket extends Intangible
{
    /**
     * The date the ticket was issued.
     * @var string|int|DateTime|null
     */
    public null|string|int|DateTime $dateIssued ;

    /**
     * The organization issuing the item, for example a Permit, Ticket, or Certification.
     * @var Organization|null
     */
    public ?Organization $issuedBy ;

    /**
     * The currency of the price, or a price component when attached to PriceSpecification and its subtypes.
     * Use standard formats: ISO 4217 currency format, e.g. "USD"; Ticker symbol for cryptocurrencies, e.g. "BTC"; well known names for Local Exchange Trading Systems (LETS) and other currency types, e.g. "Ithaca HOUR".
     * @var string|DefinedTerm|null
     */
    public null|string|DefinedTerm $priceCurrency ;

    /**
     * The unique identifier for the ticket.
     * @var string|null
     */
    public ?string $ticketNumber ;

    /**
     * Reference to an asset (e.g., Barcode, QR code image or PDF) usable for entrance.
     * @var string|null
     */
    public ?string $ticketToken ;

    /**
     * The seat associated with the ticket.
     * @var array|Seat|null
     */
    public null|array|Seat $ticketedSeat ;

    /**
     * The total price for the reservation or ticket, including applicable taxes, shipping, etc.
     * Usage guidelines:
     * Use values from 0123456789 (Unicode 'DIGIT ZERO' (U+0030) to 'DIGIT NINE' (U+0039)) rather than superficially similar Unicode symbols.
     * Use '.' (Unicode 'FULL STOP' (U+002E)) rather than ',' to indicate a decimal point. Avoid using these symbols as a readability separator.
     * @var null|float|int|PriceSpecification|string
     */
    public null|float|int|PriceSpecification|string $totalPrice ;

    /**
     * The person or organization the reservation or ticket is for.
     * @var Organization|Person|null
     */
    public null|Organization|Person $underName ;
}