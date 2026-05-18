# Events — `org\schema\events`

The `org\schema\events` namespace is **deliberately small**: most event classes are exposed at the top level of `org\schema` and the dedicated sub-namespace only carries specialised event sub-types and enumerations that don't belong in the core.

> 🇫🇷 Cette page existe aussi en [français](../../fr/schema-org/events.md).

---

## When to use it

For most event use cases, reach for the top-level `Event` class documented in [core types](core.md). Drop into `org\schema\events` only when you need:

- `DeliveryEvent` — a delivery touchpoint along a parcel's journey,
- the dedicated event enumerations under [`events/enumerations/`](../../../src/org/schema/events/enumerations) (event statuses, attendance modes, music album types, …).

The complete Schema.org event hierarchy (`BusinessEvent`, `EducationEvent`, `MusicEvent`, `ScreeningEvent`, `SportsEvent`, …) lives directly under `org\schema` so it stays as discoverable as `Person` or `Place`.

---

## Quick example

```php
use org\schema\Event;
use org\schema\Place;
use org\schema\events\DeliveryEvent;
use org\schema\constants\Schema;

$event = new Event
([
    Schema::NAME       => 'Oihana Conference 2026' ,
    Schema::START_DATE => '2026-09-15T09:00:00+02:00' ,
    Schema::END_DATE   => '2026-09-16T18:00:00+02:00' ,
    Schema::LOCATION   => new Place([ Schema::NAME => 'Nantes' ]) ,
]);

$delivery = new DeliveryEvent
([
    Schema::NAME       => 'Out for delivery' ,
    Schema::AVAILABLE_FROM => '2026-05-19T08:00:00Z' ,
    Schema::AVAILABLE_THROUGH => '2026-05-19T20:00:00Z' ,
]);
```

---

## Class catalog

| Class           | Role                                                                    |
|-----------------|-------------------------------------------------------------------------|
| `DeliveryEvent` | A delivery touchpoint along a parcel's journey.                          |

For the enumerations attached to events (status, attendance mode, music album type, …), browse [`src/org/schema/events/enumerations/`](../../../src/org/schema/events/enumerations).

---

## Up to

[← `org\schema` overview](README.md)
