# Énumérations — `org\schema\enumerations`

Le namespace `org\schema\enumerations` rassemble les **~86 types d'énumérations Schema.org** — les listes fermées de valeurs constantes acceptées par les propriétés Schema.org (méthodes de livraison, types de méthodes de paiement, jours de la semaine, disponibilité des items, …).

> 🇬🇧 This page is also available in [English](../../en/schema-org/enumerations.md).

---

## Quand l'utiliser

Utilisez ces classes chaque fois qu'une propriété Schema.org attend une valeur parmi un ensemble fixe et bien défini :

- la disponibilité d'une `Offer` (`ItemAvailability`),
- le jour de la semaine d'une `OpeningHoursSpecification` (`DayOfWeek`),
- la méthode de livraison d'un `OfferShippingDetails` (`DeliveryMethod`),
- l'état d'un produit ou d'un service (`OfferItemCondition`),
- la méthode de paiement acceptée par une offre (`PaymentMethodType`),
- la fonction commerciale d'une `Demand` ou `Offer` (`BusinessFunction`),
- le statut d'un événement, d'une action ou d'une commande (`StatusEnumeration` et ses enfants spécialisés).

---

## Exemple express

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

## Catalogue des classes (points clés)

| Thème                       | Classes                                                                                          |
|-----------------------------|--------------------------------------------------------------------------------------------------|
| Temps                       | `DayOfWeek`                                                                                       |
| État d'item & d'offre       | `ItemAvailability`, `OfferItemCondition`, `StatusEnumeration`                                     |
| Livraison & logistique      | `DeliveryMethod`, `WarrantyScope`                                                                 |
| Paiement                    | `PaymentMethodType`, `PriceComponentTypeEnumeration`, `PriceTypeEnumeration`                      |
| Activité & contact          | `BusinessFunction`, `BusinessEntityType`, `ContactPointOption`                                    |
| Public & contenu            | `AdultOrientedEnumeration`, `MediaEnumeration`, `HearingImpairedSupported`                        |
| Santé                       | `MedicalSpeciality`, `Specialty`                                                                  |
| Non lucratif & incitations  | `NonprofitType`, `IncentiveType`                                                                  |
| Téléphonie                  | `TollFree`                                                                                        |

Des sous-dossiers organisent des énumérations supplémentaires par domaine : [`enumerations/conditions/`](../../../src/org/schema/enumerations/conditions), [`enumerations/days/`](../../../src/org/schema/enumerations/days), [`enumerations/events/`](../../../src/org/schema/enumerations/events), [`enumerations/medias/`](../../../src/org/schema/enumerations/medias), [`enumerations/permissions/`](../../../src/org/schema/enumerations/permissions), [`enumerations/status/`](../../../src/org/schema/enumerations/status), [`enumerations/types/`](../../../src/org/schema/enumerations/types).

Pour la liste exhaustive, parcourez [`src/org/schema/enumerations/`](../../../src/org/schema/enumerations).

---

## Retour

[← Vue d'ensemble `org\schema`](README.md)
