# `xyz\oihana\schema\shipping` — Expédition Oihana

L'espace de noms `xyz\oihana\schema\shipping` modélise **la façon dont la marchandise atteint une adresse** — non pas la livraison elle-même, que Schema.org couvre déjà avec `ParcelDelivery`, mais les dispositions permanentes qu'un back-office entretient autour d'elle.

> 🇬🇧 This page is also available in [English](../../en/oihana/shipping.md).

---

## Quand l'utiliser

Ces classes servent lorsqu'une adresse est desservie de façon **récurrente**, et non expédition par expédition :

- une *DeliveryRouteAssignment* lorsqu'une tournée de livraison s'arrête à une adresse donnée — quels jours parmi les siens, dans quel ordre de passage, dans quelles plages horaires.

La tournée elle-même n'est pas ici : c'est un terme de thésaurus, [`DeliveryRouteTerm`](thesaurus.md), parce qu'un back-office entretient ses circuits avec le reste de ses référentiels. Cet espace de noms porte ce que seul l'**appariement** d'une tournée et d'une adresse connaît.

Chaque entité expose le distinguateur `@context = 'https://schema.oihana.xyz'` dans la sortie JSON-LD.

---

## Tournée, méthode, affectation

Trois questions, trois réponses, délibérément séparées :

| Question | Où elle trouve sa réponse |
|---|---|
| *Comment* la commande voyage-t-elle — retrait au comptoir, transporteur, camion en propre ? | `DeliveryMethodTerm`, référencé par `Site::$deliveryMethod` et `ParcelDelivery::$hasDeliveryMethod` |
| *Sur quel passage* voyage-t-elle ? | `DeliveryRouteTerm`, référencé par `ParcelDelivery::$hasDeliveryRoute` |
| *Quelles tournées desservent cette adresse, et quand* ? | `DeliveryRouteAssignment`, listée par `Site::$deliveryRoute` |

Tournée et méthode sont orthogonales : une douzaine de circuits peuvent tous rouler sous l'unique méthode « camion en propre », et une tournée ne porte aucun frais qui lui soit propre — le port reste sur la méthode.

---

## Seule la référence est stockée

Une affectation porte la tournée sous forme de **référence nue** (`route`), que le référentiel résout en `DeliveryRouteTerm` une fois la jointure faite. Rien ne recopie le libellé de la tournée : renommer un circuit est une seule écriture dans le thésaurus, aucune adresse n'a besoin d'être réécrite.

Le document commercial est l'exception délibérée : il fige l'identité de la tournée qu'il nomme, pour qu'un devis continue de dire ce qui a été choisi même après que le thésaurus a changé.

---

## Exemple rapide

```php
use org\schema\enumerations\DayOfWeek;
use xyz\oihana\schema\shipping\DeliveryRouteAssignment;

$assignment = new DeliveryRouteAssignment
([
    DeliveryRouteAssignment::ROUTE      => '01D' ,          // référence nue, résolue à la jointure
    DeliveryRouteAssignment::BY_DAY     => [ DayOfWeek::FRIDAY ] ,
    DeliveryRouteAssignment::POSITION   => 12 ,             // douzième arrêt de la tournée
    DeliveryRouteAssignment::START_TIME => '08:00' ,        // aucune borne de fermeture
]);
```

Relue depuis une adresse, une fois la tournée jointe :

```json
{
  "@type": "DeliveryRouteAssignment",
  "@context": "https://schema.oihana.xyz",
  "route": { "@type": "DeliveryRouteTerm", "id": "01D", "name": "Ouest, milieu de semaine" },
  "byDay": [ "http://purl.org/goodrelations/v1#Friday" ],
  "position": 12,
  "startTime": "08:00"
}
```

---

## Les deux `byDay`, et pourquoi les deux existent

`DeliveryRouteTerm::$byDay` dit quand la tournée **roule**. `DeliveryRouteAssignment::$byDay` dit quand elle **dessert une adresse donnée**, et c'est toujours un sous-ensemble : un circuit sur la route le lundi, le mercredi et le vendredi peut ne s'arrêter à telle adresse que le vendredi.

Les deux emploient le vocabulaire `DayOfWeek` de `Schedule::$byDay` : un consommateur qui sait déjà lire un calendrier lit une tournée avec le même code.

---

## Catalogue des classes

| Classe | Étend | Rôle |
|---|---|---|
| `DeliveryRouteAssignment` | `StructuredValue` | L'appariement d'une tournée et d'une adresse : `route` (référence ou terme résolu), `byDay`, `position` (ordre de passage), `startTime` / `endTime` (bornes `HH:MM`), `weekFrom` / `weekThrough` (numéros de semaine ISO, pour un arrêt qui n'existe qu'une partie de l'année). |

Pour la liste exhaustive des propriétés, parcourez les sources sous [`src/xyz/oihana/schema/shipping/`](../../../src/xyz/oihana/schema/shipping) ou la [référence d'API](../../../docs).

---

## Hydratation

[`hydrateDeliveryRouteAssignment()`](../../../src/xyz/oihana/schema/helpers/hydrate/hydrateDeliveryRouteAssignment.php) transforme les lignes stockées en affectations — une seule, ou la liste qui est la forme habituelle — et résout chaque `route` imbriquée en `DeliveryRouteTerm` lorsqu'elle porte une ligne de référence jointe. Un code nu est laissé tel quel : rien n'a été joint, et fabriquer un terme à partir d'une chaîne reviendrait à prétendre un libellé que personne n'a lu.

L'assistant est appelé par [`hydrateCustomerSite()`](helpers.md), si bien qu'une adresse ressort avec ses tournées déjà typées. Par la voie de la réflexion, l'attribut `#[HydrateWith]` posé sur `Site::$deliveryRoute` fait le même travail.

Côté livraison, c'est [`hydrateParcelDelivery()`](../../../src/xyz/oihana/schema/helpers/hydrate/hydrateParcelDelivery.php) qui type les deux propriétés nommées plus haut : `ParcelDelivery::$hasDeliveryMethod` en `DeliveryMethodTerm`, `ParcelDelivery::$hasDeliveryRoute` en `DeliveryRouteTerm` — et l'adresse de livraison en `PostalAddress` au passage. La réflexion **ne peut pas** s'en charger ici : `ParcelDelivery` appartient à `org\schema` et ne déclare aucun attribut sur ces propriétés, précisément parce qu'un attribut nommant nos termes de thésaurus ferait dépendre le miroir Schema.org de la couche métier. Les deux classes cibles sont donc des **paramètres** de l'assistant, avec les termes maison par défaut.

---

## Constantes associées

Les clés de propriétés sont exposées par le trait de constantes [`DeliveryRouteAssignment`](../../../src/xyz/oihana/schema/constants/traits/shipping/DeliveryRouteAssignment.php), agrégé via [`ShippingTrait`](../../../src/xyz/oihana/schema/constants/traits/ShippingTrait.php) dans la classe maîtresse [`Oihana`](../../../src/xyz/oihana/schema/constants/Oihana.php) — chaque clé est donc joignable en `Oihana::ROUTE`, `Oihana::BY_DAY`, `Oihana::WEEK_FROM`, etc.

Quatre des sept clés (`byDay`, `position`, `startTime`, `endTime`) sont redéclarées avec la valeur qu'elles portent déjà ailleurs dans la bibliothèque. C'est le motif maison : une redéclaration identique se compose sans conflit, et chaque entité garde un vocabulaire autonome. Une valeur **différente**, elle, serait fatale dès que les deux traits se rencontrent dans l'agrégateur.

---

## Lectures associées

- [`xyz\oihana\schema\thesaurus`](thesaurus.md) — `DeliveryRouteTerm`, `DeliveryMethodTerm`.
- [`xyz\oihana\schema\places`](places.md) — `Site::$deliveryRoute`, l'autre bout de l'appariement.
- [`org\schema`](../schema-org/README.md) — `ParcelDelivery`, `Schedule`, `DayOfWeek`, `StructuredValue`.
- [Référence d'API](../../../docs).
