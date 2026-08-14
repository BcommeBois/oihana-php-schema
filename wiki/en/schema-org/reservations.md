# Reservations — `org\schema\reservations`

A reservation describes what a confirmation actually contains: **who** booked, **what**, **under which name**, **for how much** and **in which state**. The base class `org\schema\Reservation` carries that shared vocabulary; the `org\schema\reservations` sub-namespace only adds what each domain has of its own.

> 🇫🇷 Cette page existe aussi en [français](../../fr/schema-org/reservations.md).

---

## When to use it

To describe an **actual** reservation — a ticket issued, a room confirmed, a table held. For an *offer* of a ticket, a table or a car, reach for `Offer` instead: one proposes, the other commits.

---

## Quick example

```php
use org\schema\constants\Schema;
use org\schema\Event;
use org\schema\Person;
use org\schema\Ticket;
use org\schema\enumerations\status\ReservationStatusType;
use org\schema\reservations\EventReservation;

$reservation = new EventReservation
([
    Schema::RESERVATION_ID     => 'RES-42' ,
    Schema::RESERVATION_STATUS => ReservationStatusType::RESERVATION_CONFIRMED ,
    Schema::BOOKING_TIME       => '2026-08-14T10:00:00+02:00' ,
    Schema::PRICE_CURRENCY     => 'EUR' ,
    Schema::TOTAL_PRICE        => 149.90 ,
    Schema::RESERVATION_FOR    => new Event([ Schema::NAME => 'Oihana Conference 2026' ]) ,
    Schema::UNDER_NAME         => new Person([ Schema::NAME => 'Ada Lovelace' ]) ,
    Schema::RESERVED_TICKET    => new Ticket([ Schema::TICKET_NUMBER => 'T-42' ]) ,
]);
```

---

## What the base class carries

| Property                 | Role                                                                  |
|--------------------------|-----------------------------------------------------------------------|
| `bookingTime`            | When the reservation was made.                                         |
| `modifiedTime`           | When it was last changed.                                              |
| `broker`                 | The intermediary who arranged the exchange.                            |
| `provider`               | Who performs the reserved service.                                     |
| `programMembershipUsed`  | The loyalty program applied to it.                                     |
| `reservationFor`         | **What** is reserved — a flight, an event, a restaurant, a place.      |
| `reservationId`          | The unique identifier of the reservation.                              |
| `reservationStatus`      | Its state (see `ReservationStatusType`).                               |
| `reservedTicket`         | The ticket associated with it.                                         |
| `priceCurrency`, `totalPrice` | What is owed, and in which currency.                              |
| `underName`              | The person or organization it is held for.                             |

🔑 **`reservationFor` carries a `Thing`**, not just a party to the booking: the one property saying *what* was reserved has to hold an `Event`, a `Place`, a `FoodEstablishment` or a car.

⚠️ **The ticket property is `reservedTicket`** — the spelling Schema.org publishes. A payload written with any other key is dropped on the way in, without a word.

---

## Class catalog

| Class                           | What it adds                                                                                               |
|---------------------------------|------------------------------------------------------------------------------------------------------------|
| `BoatReservation`               | Nothing — the base vocabulary is enough.                                                                    |
| `BusReservation`                | Nothing.                                                                                                    |
| `EventReservation`              | Nothing.                                                                                                    |
| `TrainReservation`              | Nothing.                                                                                                    |
| `FlightReservation`             | `boardingGroup`, `passengerPriorityStatus`, `passengerSequenceNumber`, `securityScreening`.                  |
| `FoodEstablishmentReservation`  | `startTime`, `endTime`, `partySize`.                                                                        |
| `LodgingReservation`            | `checkinTime`, `checkoutTime`, `lodgingUnitDescription`, `lodgingUnitType`, `numAdults`, `numChildren`.      |
| `RentalCarReservation`          | `pickupLocation`, `pickupTime`, `dropoffLocation`, `dropoffTime`.                                            |
| `TaxiReservation`               | `pickupLocation`, `pickupTime`, `partySize`.                                                                 |
| `ReservationPackage`            | `subReservation` — the reservations the package groups.                                                     |

---

## Statuses

`org\schema\enumerations\status\ReservationStatusType` carries the four members Schema.org publishes:

```php
use org\schema\enumerations\status\ReservationStatusType;

ReservationStatusType::RESERVATION_CANCELLED ; // 'https://schema.org/ReservationCancelled'
ReservationStatusType::RESERVATION_CONFIRMED ; // 'https://schema.org/ReservationConfirmed'
ReservationStatusType::RESERVATION_HOLD      ; // 'https://schema.org/ReservationHold'
ReservationStatusType::RESERVATION_PENDING   ; // 'https://schema.org/ReservationPending'

ReservationStatusType::includes( $reservation->reservationStatus ) ; // validates an incoming value
```

Each member also exists as a **class**, in the same `enumerations/status/` folder — `ReservationCancelled`, `ReservationConfirmed`, `ReservationHold`, `ReservationPending`. Both forms say the same thing: the serialized `@type` of a member class is exactly the URI its constant carries. Reach for the constant to set a status, for the class when you describe the status itself as an entity.

---

## Tickets

`org\schema\Ticket` carries what tells a ticket apart from the reservation holding it: `ticketNumber`, `ticketToken` (the barcode or QR code), `ticketedSeat`, `dateIssued`, `issuedBy`.

---

## Up to

[← `org\schema` overview](README.md)
