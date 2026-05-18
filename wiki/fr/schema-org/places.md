# Lieux — `org\schema\places`

Le namespace `org\schema\places` contient les **~26 types de lieux spécialisés** qui étendent `org\schema\Place` — zones administratives, structures civiques, étendues d'eau, monuments, hébergements et destinations touristiques.

> 🇬🇧 This page is also available in [English](../../en/schema-org/places.md).

---

## Quand l'utiliser

Utilisez ces classes chaque fois que vous avez besoin d'une description plus précise que le `Place` générique — par exemple :

- décrire une région administrative (`Country`, `State`, `City`, `AdministrativeArea`, `DefinedRegion`, `SchoolDistrict`),
- modéliser un élément naturel (`Mountain`, `Volcano`, `Continent`, `Landform`, `BodyOfWater` et ses sous-types),
- représenter des infrastructures civiques et touristiques (`CivicStructure`, `LandmarksOrHistoricalBuildings`, `TouristAttraction`, `TouristDestination`),
- décrire un hébergement (`Accommodation`, `Residence`).

Pour les emplacements opérationnels spécifiques Oihana (Site, Office, Warehouse, JobSite), utilisez plutôt le namespace [`xyz\oihana\schema\places`](../oihana-places.md).

---

## Exemple express

```php
use org\schema\PostalAddress;
use org\schema\places\Country;
use org\schema\places\City;
use org\schema\constants\Schema;

$city = new City
([
    Schema::NAME    => 'Nantes' ,
    Schema::ADDRESS => new PostalAddress
    ([
        Schema::ADDRESS_LOCALITY => 'Nantes' ,
        Schema::POSTAL_CODE      => '44000' ,
        Schema::ADDRESS_COUNTRY  => new Country([ Schema::NAME => 'France' ]) ,
    ]),
]);
```

---

## Catalogue des classes

| Catégorie               | Classes                                                                                         |
|-------------------------|-------------------------------------------------------------------------------------------------|
| Zones administratives   | `AdministrativeArea`, `Country`, `State`, `City`, `DefinedRegion`, `SchoolDistrict`, `Continent` |
| Éléments naturels       | `Landform`, `Mountain`, `Volcano`, `BodyOfWater`, `Canal`, `LakeBodyOfWater`, `OceanBodyOfWater`, `Pond`, `Reservoir`, `RiverBodyOfWater`, `SeaBodyOfWater`, `Waterfall` |
| Infrastructure civique  | `CivicStructure`, `LandmarksOrHistoricalBuildings`                                               |
| Hébergement             | `Accommodation`, `Residence`                                                                    |
| Tourisme                | `TouristAttraction`, `TouristDestination`                                                       |
| Commerce                | `LocalBusiness` (également exposé sous [organisations](organizations.md))                        |

Pour la liste exhaustive, parcourez [`src/org/schema/places/`](../../../src/org/schema/places).

---

## Retour

[← Vue d'ensemble `org\schema`](README.md)
