# Enumerations — `org\schema\enumerations`

The `org\schema\enumerations` namespace gathers the **~86 Schema.org enumeration types** — the closed lists of constant values that Schema.org properties accept (delivery methods, payment method types, days of the week, item availability, …).

> 🇫🇷 Cette page existe aussi en [français](../../fr/schema-org/enumerations.md).

---

## When to use it

Use these classes whenever a Schema.org property expects one of a fixed, well-defined set of values:

- the availability of an `Offer` (`ItemAvailability`),
- the day of the week of an `OpeningHoursSpecification` (`DayOfWeek`),
- the delivery method of an `OfferShippingDetails` (`DeliveryMethod`),
- the condition of a product or service (`OfferItemCondition`),
- the payment method accepted by an offer (`PaymentMethodType`),
- the business function of a `Demand` or `Offer` (`BusinessFunction`),
- the status of an event, action or order (`StatusEnumeration` and its specialised children).

---

## Quick example

```php
use org\schema\Offer;
use org\schema\enumerations\ItemAvailability;
use org\schema\enumerations\DeliveryMethod;
use org\schema\constants\Schema;

$offer = new Offer
([
    Schema::PRICE          => '249.00' ,
    Schema::PRICE_CURRENCY => 'EUR' ,
    Schema::AVAILABILITY   => ItemAvailability::IN_STOCK ,
    Schema::AVAILABLE_DELIVERY_METHOD => DeliveryMethod::ON_SITE_PICKUP ,
]);
```

---

## Class catalog (highlights)

| Topic                       | Classes                                                                                          |
|-----------------------------|--------------------------------------------------------------------------------------------------|
| Time                        | `DayOfWeek`                                                                                       |
| Item & offer state          | `ItemAvailability`, `OfferItemCondition`, `StatusEnumeration`                                     |
| Delivery & logistics        | `DeliveryMethod`, `WarrantyScope`                                                                 |
| Payment                     | `PaymentMethodType`, `PriceComponentTypeEnumeration`, `PriceTypeEnumeration`                      |
| Business & contact          | `BusinessFunction`, `BusinessEntityType`, `ContactPointOption`                                    |
| Audience & content          | `AdultOrientedEnumeration`, `MediaEnumeration`, `HearingImpairedSupported`                        |
| Health                      | `MedicalSpeciality`, `Specialty`                                                                  |
| Nonprofit & incentives      | `NonprofitType`, `IncentiveType`                                                                  |
| Telephony                   | `TollFree`                                                                                        |

Sub-folders organise additional enumerations by domain: [`enumerations/conditions/`](../../../src/org/schema/enumerations/conditions), [`enumerations/days/`](../../../src/org/schema/enumerations/days), [`enumerations/events/`](../../../src/org/schema/enumerations/events), [`enumerations/medias/`](../../../src/org/schema/enumerations/medias), [`enumerations/permissions/`](../../../src/org/schema/enumerations/permissions), [`enumerations/status/`](../../../src/org/schema/enumerations/status), [`enumerations/types/`](../../../src/org/schema/enumerations/types).

For the exhaustive list, browse [`src/org/schema/enumerations/`](../../../src/org/schema/enumerations).

---

## Up to

[← `org\schema` overview](README.md)
