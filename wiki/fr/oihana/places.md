# `xyz\oihana\schema\places` — Lieux Oihana

Le namespace `xyz\oihana\schema\places` ajoute des **types de lieux opérationnels et métier** absents du cœur de Schema.org. Il modélise des emplacements concrets au sein d'une organisation (sites, bureaux, entrepôts, chantiers) tout en restant compatible avec le vocabulaire `Place` standard.

> 🇬🇧 This page is also available in [English](../../en/oihana/places.md).

---

## Quand l'utiliser

Choisissez ces classes dès que vous avez besoin de décrire **où se passe l'activité** avec une sémantique plus riche qu'un simple `Place` :

- un *Site* avec ses méthodes de livraison, les tournées qui le desservent et son organisation propriétaire,
- un *Office* (bureau) ou un *Warehouse* (entrepôt) utilisé par une organisation,
- un *JobSite* (chantier) temporaire rattaché à un projet.

Chaque classe étend `org\schema\Place`, donc elle hérite de toutes les propriétés d'adresse, de géolocalisation, de téléphone et `sameAs` de Schema.org, tout en exposant le distinguisheur `@context = 'https://schema.oihana.xyz'` dans le JSON-LD.

---

## Exemple express

```php
use org\schema\enumerations\DayOfWeek;
use org\schema\PostalAddress;
use org\schema\Organization;
use xyz\oihana\schema\places\Site;
use xyz\oihana\schema\shipping\DeliveryRouteAssignment;

$site = new Site
([
    'name'    => 'Entrepôt principal' ,
    'address' => new PostalAddress
    ([
        'streetAddress'   => '2 chemin des Vergers' ,
        'postalCode'      => '49170' ,
        'addressLocality' => 'Saint-Georges-sur-Loire' ,
    ]),
    'ownedBy'       => new Organization([ 'name' => 'Oihana SAS' ]) ,
    'deliveryMethod' => 'OnSitePickup' ,
    'position'       => 1 ,
    'deliveryRoute'  =>
    [
        new DeliveryRouteAssignment
        ([
            DeliveryRouteAssignment::ROUTE  => '01D' ,       // résolue en terme à la jointure
            DeliveryRouteAssignment::BY_DAY => [ DayOfWeek::FRIDAY ] ,
        ]),
    ],
]);

echo json_encode( $site , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

---

## Catalogue des classes

| Classe      | Étend     | Rôle                                                                                              |
|-------------|-----------|---------------------------------------------------------------------------------------------------|
| `Site`      | `Place`   | Site opérationnel polyvalent (site client, site fournisseur, emplacement industriel). Ajoute `contactPoint`, `deliveryMethod`, `deliveryRoute`, `ownedBy`, `position`. |
| `Office`    | `Place`   | Immeuble ou étage de bureaux utilisé par une organisation.                                        |
| `Warehouse` | `Place`   | Entrepôt — généralement avec méthodes de livraison, capacité et horaires d'ouverture.             |
| `JobSite`   | `Place`   | Lieu rattaché à un projet (chantier, intervention sur site, déploiement temporaire).              |

Pour la liste exhaustive des propriétés, parcourez le code source sous [`src/xyz/oihana/schema/places/`](../../../src/xyz/oihana/schema/places) ou la [référence d'API](../../../docs).

### Quelles tournées desservent un site

`Site::$deliveryRoute` liste les tournées de livraison qui s'arrêtent à l'adresse, sous forme de valeurs [`DeliveryRouteAssignment`](shipping.md) — une par tournée, car une même adresse est couramment visitée par plusieurs circuits, chacun sur ses propres jours et dans son propre ordre de passage.

Seule la **référence** de la tournée est portée, jamais une copie de son libellé : renommer un circuit est une seule écriture dans le thésaurus. L'attribut `#[HydrateWith]` transforme les lignes stockées en affectations par la voie de la réflexion, et [`hydrateCustomerSite()`](helpers.md) fait de même par la voie des assistants.

---

## Constantes associées

Les clés de propriétés spécifiques aux sites sont exposées par le trait [`Site`](../../../src/xyz/oihana/schema/constants/traits/places/Site.php), composé dans l'agrégateur principal [`Oihana`](../../../src/xyz/oihana/schema/constants/Oihana.php). Vous pouvez donc y accéder via `Oihana::DELIVERY_METHOD`, `Oihana::DELIVERY_ROUTE`, `Oihana::OWNED_BY`, `Oihana::POSITION`, etc.

---

## Pour aller plus loin

- [`org\schema`](../schema-org/README.md) — `Place`, `PostalAddress`, `Organization`, `Person`.
- [Démarrage rapide](../demarrage.md) — installation, hydratation, bases du JSON-LD.
- [Référence d'API](../../../docs).
