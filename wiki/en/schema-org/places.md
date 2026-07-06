# Places — `org\schema\places`

The `org\schema\places` namespace contains the **~26 specialised place types** that extend `org\schema\Place` — administrative areas, civic structures, bodies of water, landmarks, accommodations and tourism destinations.

> 🇫🇷 Cette page existe aussi en [français](../../fr/schema-org/places.md).

---

## When to use it

Reach for these classes whenever you need a more precise description than the generic `Place` — for instance:

- describe an administrative region (`Country`, `State`, `City`, `AdministrativeArea`, `DefinedRegion`, `SchoolDistrict`),
- model a natural feature (`Mountain`, `Volcano`, `Continent`, `Landform`, `BodyOfWater` and its subtypes),
- represent civic and tourism infrastructure (`CivicStructure`, `LandmarksOrHistoricalBuildings`, `TouristAttraction`, `TouristDestination`),
- describe lodging (`Accommodation`, `Residence`).

For Oihana-specific operational locations (Site, Office, Warehouse, JobSite), use the [`xyz\oihana\schema\places`](../oihana/places.md) namespace instead.

---

## Quick example

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

## Class catalog

| Category               | Classes                                                                                          |
|------------------------|--------------------------------------------------------------------------------------------------|
| Administrative areas   | `AdministrativeArea`, `Country`, `State`, `City`, `DefinedRegion`, `SchoolDistrict`, `Continent`  |
| Natural features       | `Landform`, `Mountain`, `Volcano`, `BodyOfWater`, `Canal`, `LakeBodyOfWater`, `OceanBodyOfWater`, `Pond`, `Reservoir`, `RiverBodyOfWater`, `SeaBodyOfWater`, `Waterfall` |
| Civic infrastructure   | `CivicStructure`, `LandmarksOrHistoricalBuildings`                                                |
| Lodging                | `Accommodation`, `Residence`                                                                     |
| Tourism                | `TouristAttraction`, `TouristDestination`                                                        |
| Commerce               | `LocalBusiness` (also exposed under [organizations](organizations.md))                            |

For the exhaustive list, browse [`src/org/schema/places/`](../../../src/org/schema/places).

---

## Up to

[← `org\schema` overview](README.md)
