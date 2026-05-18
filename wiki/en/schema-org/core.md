# Core types — `org\schema`

The root of the `org\schema` namespace holds the **~100 most fundamental Schema.org types**: the common nouns of the vocabulary that everything else extends or references.

> 🇫🇷 Cette page existe aussi en [français](../../fr/schema-org/core.md).

---

## When to use it

Pretty much every JSON-LD document you produce will start here. The core types describe:

- **Agents** — `Person`, `Organization`, `Audience`, `Brand`,
- **Places** — `Place`, `PostalAddress`, `GeoCoordinates`, `GeoShape`, `GeoCircle`,
- **Events** — `Event` (base type for the [events guide](events.md)),
- **Commerce** — `Product`, `Offer`, `Order`, `OrderItem`, `PriceSpecification`, `MerchantReturnPolicy`,
- **Quantities & units** — `Quantity`, `QuantitativeValue`, `Duration`, `Distance`, `Mass`, `Energy`,
- **Identifiers & terms** — `Thing` (root), `Intangible`, `DefinedTerm`, `Keyword`, `Language`,
- **Memberships & permits** — `ProgramMembership`, `MemberProgram`, `MemberProgramTier`, `Permit`, `GovernmentPermit`,
- **Grants & finance** — `Grant`, `FinancialIncentive`, `Invoice`, `MonetaryAmount`.

---

## Quick example

```php
use org\schema\Offer;
use org\schema\Organization;
use org\schema\Product;
use org\schema\QuantitativeValue;
use org\schema\constants\Schema;

$product = new Product
([
    Schema::NAME        => 'Oihana Conference 2026 — Early Bird' ,
    Schema::DESCRIPTION => 'Two-day technical conference in Nantes.' ,
    Schema::OFFERS      => new Offer
    ([
        Schema::PRICE          => '249.00' ,
        Schema::PRICE_CURRENCY => 'EUR' ,
        Schema::SELLER         => new Organization([ Schema::NAME => 'Oihana SAS' ]) ,
        Schema::ELIGIBLE_QUANTITY => new QuantitativeValue
        ([
            Schema::MIN_VALUE => 1 ,
            Schema::MAX_VALUE => 4 ,
        ]),
    ]),
]);
```

---

## Class catalog (highlights)

| Category              | Classes                                                                                  |
|-----------------------|------------------------------------------------------------------------------------------|
| Root                  | `Thing`, `Intangible`, `Enumeration`, `Type`                                              |
| Agents                | `Person`, `Organization`, `Audience`, `Brand`, `OwnershipInfo`                            |
| Places & geo          | `Place`, `PostalAddress`, `GeoCoordinates`, `GeoShape`, `GeoCircle`, `GeospatialGeometry`, `LocationFeatureSpecification` |
| Events                | `Event` (base — specialized children under [events](events.md))                            |
| Commerce              | `Product`, `Offer`, `OfferShippingDetails`, `OfferForPurchase`, `OfferCatalog`, `AggregateOffer`, `Order`, `OrderItem`, `Demand`, `ContactPoint`, `MerchantReturnPolicy`, `ParcelDelivery` |
| Pricing & payments    | `PriceSpecification`, `CompoundPriceSpecification`, `DeliveryChargeSpecification`, `MonetaryAmount`, `PaymentMethod`, `RepaymentSpecification`, `Invoice` |
| Quantities & units    | `Quantity`, `QuantitativeValue`, `QualitativeValue`, `Duration`, `Distance`, `Mass`, `Energy`, `SizeSpecification` |
| Identifiers & terms   | `DefinedTerm`, `Keyword`, `Language`, `CategoryCode`, `Context`                          |
| Ratings & reviews     | `Rating`, `AggregateRating`, `EndorsementRating`, `Review`                               |
| Service & jobs        | `Service`, `ServiceChannel`, `Occupation`, `OccupationExperienceRequirements`, `Seat`     |
| Memberships & permits | `ProgramMembership`, `MemberProgram`, `MemberProgramTier`, `Permit`, `GovernmentPermit`, `Grant`, `FinancialIncentive` |
| Specifications        | `PropertyValue`, `PropertyValueSpecification`, `AlignmentObject`, `ConstraintNode`, `OpeningHoursSpecification` |
| Misc                  | `Action` (base — see [actions](actions.md)), `CreativeWork` (base — see [creative-work](creative-work.md)), `ItemList`, `ListItem`, `DataFeedItem`, `InteractionCounter` |

For the exhaustive list, browse [`src/org/schema/`](../../../src/org/schema) directly.

---

## Up to

[← `org\schema` overview](README.md)
