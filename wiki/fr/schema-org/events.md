# Événements — `org\schema\events`

Le namespace `org\schema\events` est **volontairement réduit** : la plupart des classes d'événements sont exposées au sommet de `org\schema` et le sous-namespace dédié n'embarque que les sous-types d'événements spécialisés et les énumérations qui n'ont pas leur place au cœur du namespace.

> 🇬🇧 This page is also available in [English](../../en/schema-org/events.md).

---

## Quand l'utiliser

Pour la plupart des cas d'usage liés aux événements, utilisez la classe `Event` de premier niveau documentée dans [types de base](core.md). Descendez dans `org\schema\events` uniquement si vous avez besoin :

- de `DeliveryEvent` — un point de contact de livraison le long du trajet d'un colis,
- des énumérations dédiées aux événements sous [`events/enumerations/`](../../../src/org/schema/events/enumerations) (statuts d'événement, modes de présence, types d'albums musicaux, …).

La hiérarchie complète des événements Schema.org (`BusinessEvent`, `EducationEvent`, `MusicEvent`, `ScreeningEvent`, `SportsEvent`, …) vit directement sous `org\schema` afin de rester aussi accessible que `Person` ou `Place`.

---

## Exemple express

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
    Schema::NAME              => 'Out for delivery' ,
    Schema::AVAILABLE_FROM    => '2026-05-19T08:00:00Z' ,
    Schema::AVAILABLE_THROUGH => '2026-05-19T20:00:00Z' ,
]);
```

---

## Catalogue des classes

| Classe          | Rôle                                                                    |
|-----------------|-------------------------------------------------------------------------|
| `DeliveryEvent` | Un point de contact de livraison le long du trajet d'un colis.           |

Pour les énumérations rattachées aux événements (statut, mode de présence, type d'album musical, …), parcourez [`src/org/schema/events/enumerations/`](../../../src/org/schema/events/enumerations).

---

## Retour

[← Vue d'ensemble `org\schema`](README.md)
