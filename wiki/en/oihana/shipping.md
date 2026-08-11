# `xyz\oihana\schema\shipping` — Oihana shipping

The `xyz\oihana\schema\shipping` namespace models **how goods reach an address** — not the delivery itself, which Schema.org already covers with `ParcelDelivery`, but the standing arrangements a back office maintains around it.

> 🇫🇷 Cette page existe aussi en [français](../../fr/oihana/shipping.md).

---

## When to use it

Reach for these classes when an address is served on a **recurring** basis rather than shipment by shipment:

- a *DeliveryRouteAssignment* when a delivery route stops at a given address — on which of its days, in which order of passage, within which hours.

The route itself is not here : it is a thesaurus term, [`DeliveryRouteTerm`](thesaurus.md), because a back office maintains its circuits alongside its other reference data. This namespace holds what only the **pairing** of a route and an address knows.

Every entity exposes the `@context = 'https://schema.oihana.xyz'` distinguisher in the JSON-LD output.

---

## Route, method, assignment

Three questions, three answers, deliberately kept apart :

| Question | Where it is answered |
|---|---|
| *How* does the order travel — counter pick-up, carrier, own fleet ? | `DeliveryMethodTerm`, referenced by `Site::$deliveryMethod` and `ParcelDelivery::$hasDeliveryMethod` |
| *On which passage* does it travel ? | `DeliveryRouteTerm`, referenced by `ParcelDelivery::$hasDeliveryRoute` |
| *Which routes serve this address, and when* ? | `DeliveryRouteAssignment`, listed by `Site::$deliveryRoute` |

Route and method are orthogonal : a dozen circuits may all be run under the single "own fleet" method, and a route carries no charge of its own — the carriage stays on the method.

---

## Only the reference is stored

An assignment carries the route as a **bare reference** (`route`), which the reference data resolves into a `DeliveryRouteTerm` when it is joined. Nothing copies the route's label, so renaming a circuit is a single write in the thesaurus — no address has to be rewritten.

A business document is the deliberate exception : it freezes the identity of the route it names, so a quote keeps saying what was chosen even after the thesaurus has moved on.

---

## Quick example

```php
use org\schema\enumerations\DayOfWeek;
use xyz\oihana\schema\shipping\DeliveryRouteAssignment;

$assignment = new DeliveryRouteAssignment
([
    DeliveryRouteAssignment::ROUTE      => '01D' ,          // bare reference, resolved once joined
    DeliveryRouteAssignment::BY_DAY     => [ DayOfWeek::FRIDAY ] ,
    DeliveryRouteAssignment::POSITION   => 12 ,             // twelfth stop of the round
    DeliveryRouteAssignment::START_TIME => '08:00' ,        // no closing constraint
]);
```

Read back from an address, once the route has been joined :

```json
{
  "@type": "DeliveryRouteAssignment",
  "@context": "https://schema.oihana.xyz",
  "route": { "@type": "DeliveryRouteTerm", "id": "01D", "name": "West coast, midweek" },
  "byDay": [ "http://purl.org/goodrelations/v1#Friday" ],
  "position": 12,
  "startTime": "08:00"
}
```

---

## The two `byDay`, and why both exist

`DeliveryRouteTerm::$byDay` says when the route **runs**. `DeliveryRouteAssignment::$byDay` says when it **serves one given address**, and is always a subset : a circuit on the road Monday, Wednesday and Friday may only stop at a particular address on Friday.

Both use the `DayOfWeek` vocabulary of `Schedule::$byDay`, so a consumer that already reads schedules reads a route with the same code.

---

## Class catalog

| Class | Extends | Purpose |
|---|---|---|
| `DeliveryRouteAssignment` | `StructuredValue` | The pairing of a delivery route with one address : `route` (reference or resolved term), `byDay`, `position` (order of passage), `startTime` / `endTime` (`HH:MM` bounds), `weekFrom` / `weekThrough` (ISO week numbers, for a stop that only exists part of the year). |

For exhaustive property lists, browse the source under [`src/xyz/oihana/schema/shipping/`](../../src/xyz/oihana/schema/shipping) or the [API reference](../../../docs).

---

## Hydration

[`hydrateDeliveryRouteAssignment()`](../../src/xyz/oihana/schema/helpers/hydrate/hydrateDeliveryRouteAssignment.php) turns stored rows into assignments — a single one, or the list that is the usual shape — and resolves each nested `route` into a `DeliveryRouteTerm` when it holds a joined reference row. A bare code is left alone : nothing has been joined, and building a term out of a string would claim a label nobody read.

The helper is called by [`hydrateCustomerSite()`](helpers.md), so an address comes out with its routes already typed. On the reflection path, `#[HydrateWith]` on `Site::$deliveryRoute` does the same work.

On the delivery side, [`hydrateParcelDelivery()`](../../src/xyz/oihana/schema/helpers/hydrate/hydrateParcelDelivery.php) is what types the two properties named above: `ParcelDelivery::$hasDeliveryMethod` into a `DeliveryMethodTerm`, `ParcelDelivery::$hasDeliveryRoute` into a `DeliveryRouteTerm` — and the delivery address into a `PostalAddress` along the way. Reflection **cannot** do it here: `ParcelDelivery` belongs to `org\schema` and declares no attribute on those properties, precisely because an attribute naming our thesaurus terms would make the Schema.org mirror depend on the business layer. The two target classes are therefore **parameters** of the helper, with the business terms as defaults.

---

## Related constants

Property keys are exposed by the [`DeliveryRouteAssignment`](../../src/xyz/oihana/schema/constants/traits/shipping/DeliveryRouteAssignment.php) constant trait, aggregated through [`ShippingTrait`](../../src/xyz/oihana/schema/constants/traits/ShippingTrait.php) into the master [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php) class — so every key is reachable as `Oihana::ROUTE`, `Oihana::BY_DAY`, `Oihana::WEEK_FROM`, etc.

Four of the seven keys (`byDay`, `position`, `startTime`, `endTime`) are redeclared with the value they already carry elsewhere in the library. That is the house pattern : an identical redeclaration composes without conflict, and each entity keeps a self-contained vocabulary. A **different** value would be fatal the moment both traits meet in the aggregator.

---

## Related reading

- [`xyz\oihana\schema\thesaurus`](thesaurus.md) — `DeliveryRouteTerm`, `DeliveryMethodTerm`.
- [`xyz\oihana\schema\places`](places.md) — `Site::$deliveryRoute`, the other end of the pairing.
- [`org\schema`](../schema-org/README.md) — `ParcelDelivery`, `Schedule`, `DayOfWeek`, `StructuredValue`.
- [API reference](../../../docs).
