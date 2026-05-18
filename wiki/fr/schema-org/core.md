# Types de base — `org\schema`

La racine du namespace `org\schema` contient les **~100 types Schema.org les plus fondamentaux** : les noms communs du vocabulaire que tous les autres étendent ou référencent.

> 🇬🇧 This page is also available in [English](../../en/schema-org/core.md).

---

## Quand l'utiliser

Pratiquement tous les documents JSON-LD que vous produirez démarreront ici. Les types de base décrivent :

- **Acteurs** — `Person`, `Organization`, `Audience`, `Brand`,
- **Lieux** — `Place`, `PostalAddress`, `GeoCoordinates`, `GeoShape`, `GeoCircle`,
- **Événements** — `Event` (type de base pour le [guide des événements](events.md)),
- **Commerce** — `Product`, `Offer`, `Order`, `OrderItem`, `PriceSpecification`, `MerchantReturnPolicy`,
- **Quantités & unités** — `Quantity`, `QuantitativeValue`, `Duration`, `Distance`, `Mass`, `Energy`,
- **Identifiants & termes** — `Thing` (racine), `Intangible`, `DefinedTerm`, `Keyword`, `Language`,
- **Adhésions & permis** — `ProgramMembership`, `MemberProgram`, `MemberProgramTier`, `Permit`, `GovernmentPermit`,
- **Subventions & finance** — `Grant`, `FinancialIncentive`, `Invoice`, `MonetaryAmount`.

---

## Exemple express

```php
use org\schema\Offer;
use org\schema\Organization;
use org\schema\Product;
use org\schema\QuantitativeValue;
use org\schema\constants\Schema;

$product = new Product
([
    Schema::NAME        => 'Oihana Conference 2026 — Early Bird' ,
    Schema::DESCRIPTION => 'Conférence technique de deux jours à Nantes.' ,
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

## Catalogue des classes (points clés)

| Catégorie             | Classes                                                                                  |
|-----------------------|------------------------------------------------------------------------------------------|
| Racine                | `Thing`, `Intangible`, `Enumeration`, `Type`                                              |
| Acteurs               | `Person`, `Organization`, `Audience`, `Brand`, `OwnershipInfo`                            |
| Lieux & géo           | `Place`, `PostalAddress`, `GeoCoordinates`, `GeoShape`, `GeoCircle`, `GeospatialGeometry`, `LocationFeatureSpecification` |
| Événements            | `Event` (base — enfants spécialisés sous [events](events.md))                              |
| Commerce              | `Product`, `Offer`, `OfferShippingDetails`, `OfferForPurchase`, `OfferCatalog`, `AggregateOffer`, `Order`, `OrderItem`, `Demand`, `ContactPoint`, `MerchantReturnPolicy`, `ParcelDelivery` |
| Tarification & paiement | `PriceSpecification`, `CompoundPriceSpecification`, `DeliveryChargeSpecification`, `MonetaryAmount`, `PaymentMethod`, `RepaymentSpecification`, `Invoice` |
| Quantités & unités    | `Quantity`, `QuantitativeValue`, `QualitativeValue`, `Duration`, `Distance`, `Mass`, `Energy`, `SizeSpecification` |
| Identifiants & termes | `DefinedTerm`, `Keyword`, `Language`, `CategoryCode`, `Context`                          |
| Notes & avis          | `Rating`, `AggregateRating`, `EndorsementRating`, `Review`                                |
| Service & emploi      | `Service`, `ServiceChannel`, `Occupation`, `OccupationExperienceRequirements`, `Seat`     |
| Adhésions & permis    | `ProgramMembership`, `MemberProgram`, `MemberProgramTier`, `Permit`, `GovernmentPermit`, `Grant`, `FinancialIncentive` |
| Spécifications        | `PropertyValue`, `PropertyValueSpecification`, `AlignmentObject`, `ConstraintNode`, `OpeningHoursSpecification` |
| Divers                | `Action` (base — voir [actions](actions.md)), `CreativeWork` (base — voir [creative-work](creative-work.md)), `ItemList`, `ListItem`, `DataFeedItem`, `InteractionCounter` |

Pour la liste exhaustive, parcourez directement [`src/org/schema/`](../../../src/org/schema).

---

## Retour

[← Vue d'ensemble `org\schema`](README.md)
