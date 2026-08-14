# Réservations — `org\schema\reservations`

Une réservation décrit ce que contient une confirmation : **qui** a réservé, **quoi**, **à quel nom**, **pour combien** et **dans quel état**. La classe de base `org\schema\Reservation` porte ce vocabulaire commun ; le sous-namespace `org\schema\reservations` n'ajoute que ce que chaque domaine a en propre.

> 🇬🇧 This page is also available in [English](../../en/schema-org/reservations.md).

---

## Quand l'utiliser

Pour décrire une réservation **effective** — un billet émis, une chambre confirmée, une table retenue. Pour une *offre* de billet, de table ou de voiture, c'est `Offer` qu'il faut, pas `Reservation` : l'une propose, l'autre acte.

---

## Exemple express

```php
use org\schema\constants\Schema;
use org\schema\Event;
use org\schema\Person;
use org\schema\Ticket;
use org\schema\enumerations\ReservationStatusType;
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

## Ce que porte la classe de base

| Propriété                | Rôle                                                                  |
|--------------------------|-----------------------------------------------------------------------|
| `bookingTime`            | Quand la réservation a été prise.                                      |
| `modifiedTime`           | Quand elle a été modifiée pour la dernière fois.                       |
| `broker`                 | L'intermédiaire qui a organisé l'échange.                              |
| `provider`               | Qui rend le service réservé.                                           |
| `programMembershipUsed`  | Le programme de fidélité appliqué.                                     |
| `reservationFor`         | **Ce qui** est réservé — un vol, un événement, un restaurant, un lieu. |
| `reservationId`          | L'identifiant unique de la réservation.                                |
| `reservationStatus`      | Son état (voir `ReservationStatusType`).                               |
| `reservedTicket`         | Le billet associé.                                                     |
| `priceCurrency`, `totalPrice` | Ce qui est dû, et dans quelle devise.                             |
| `underName`              | La personne ou l'organisation au nom de qui elle est prise.            |

🔑 **`reservationFor` porte un `Thing`**, pas seulement une partie prenante : la seule propriété qui dit *ce qui* a été réservé doit pouvoir contenir un `Event`, un `Place`, un `FoodEstablishment` ou une voiture.

⚠️ **La propriété du billet s'appelle `reservedTicket`** — l'orthographe publiée par Schema.org. Une charge utile écrite avec une autre clé est ignorée à la construction, sans un mot.

---

## Catalogue des classes

| Classe                          | Ce qu'elle ajoute                                                                                          |
|---------------------------------|------------------------------------------------------------------------------------------------------------|
| `BoatReservation`               | Rien — le vocabulaire de base suffit.                                                                       |
| `BusReservation`                | Rien.                                                                                                       |
| `EventReservation`              | Rien.                                                                                                       |
| `TrainReservation`              | Rien.                                                                                                       |
| `FlightReservation`             | `boardingGroup`, `passengerPriorityStatus`, `passengerSequenceNumber`, `securityScreening`.                  |
| `FoodEstablishmentReservation`  | `startTime`, `endTime`, `partySize`.                                                                        |
| `LodgingReservation`            | `checkinTime`, `checkoutTime`, `lodgingUnitDescription`, `lodgingUnitType`, `numAdults`, `numChildren`.      |
| `RentalCarReservation`          | `pickupLocation`, `pickupTime`, `dropoffLocation`, `dropoffTime`.                                            |
| `TaxiReservation`               | `pickupLocation`, `pickupTime`, `partySize`.                                                                 |
| `ReservationPackage`            | `subReservation` — les réservations que le forfait regroupe.                                                |

---

## États

`org\schema\enumerations\ReservationStatusType` porte les quatre membres publiés par Schema.org :

```php
use org\schema\enumerations\ReservationStatusType;

ReservationStatusType::RESERVATION_CANCELLED ; // 'https://schema.org/ReservationCancelled'
ReservationStatusType::RESERVATION_CONFIRMED ; // 'https://schema.org/ReservationConfirmed'
ReservationStatusType::RESERVATION_HOLD      ; // 'https://schema.org/ReservationHold'
ReservationStatusType::RESERVATION_PENDING   ; // 'https://schema.org/ReservationPending'

ReservationStatusType::includes( $reservation->reservationStatus ) ; // valide une valeur reçue
```

---

## Billets

`org\schema\Ticket` porte ce qui distingue un billet de la réservation qui le contient : `ticketNumber`, `ticketToken` (le code-barres ou le QR code), `ticketedSeat`, `dateIssued`, `issuedBy`.

---

## Retour

[← Vue d'ensemble `org\schema`](README.md)
