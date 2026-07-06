# Ingestion — hydrating a flat row into a nested object

**Ingestion** is the mechanism that lets a flat dataset row — an SQL row, a spreadsheet export, a projected document — hydrate a **nested** schema object directly, without manual wiring. The flat key `addressLocality` becomes the `address.addressLocality` field of a `PostalAddress`; the `mobile` key becomes a typed `ContactPoint` in the `contactPoint` collection.

The bridge is PHP's magic `__set`: when hydration assigns an **undeclared** property on the class, `__set` hands it to a chain of small specialized traits — each one recognizes its family of flat keys and builds the matching nested object.

> 🇫🇷 Cette page est aussi disponible en [français](../../fr/oihana/ingestion.md).

---

## The mechanism in one picture

```php
public function __set( string $property , mixed $value ) :void
{
    $this->setAdditionalProperties     ( $property , $value ) ||
    $this->setPostalAddressProperties  ( $property , $value ) ||
    $this->setContactPointProperty     ( $property , $value ) ;
}
```

Each link returns `true` when it recognized and absorbed the key — the chain stops at the first taker. A key no link recognizes is **silently ignored**: an extra column in the dataset breaks nothing.

---

## Quick example

```php
use xyz\oihana\schema\organizations\Customer;

// A flat row, straight out of a database or an export:
$customer = new Customer
([
    'name'             => 'South Wood Company' ,
    'addressLocality'  => 'BORDEAUX' ,
    'postalCode'       => '33000' ,
    'defaultTelephone' => '05 56 00 00 00' ,
    'mobile'           => '06 00 00 00 00' ,
]);

$customer->address->addressLocality ;        // 'BORDEAUX'      (PostalAddress)
$customer->contactPoint[0]->contactType ;    // '…/ContactType#Default'
$customer->contactPoint[1]->contactType ;    // '…/ContactType#mobile'
```

---

## The trait catalog

| Trait | Recognized flat keys | Builds |
|---|---|---|
| `SetPostalAddressTrait` | `streetAddress`, `addressLocality`, `postalCode`, `addressCountry`, `addressEmail`, `addressTelephone`, … | The `PostalAddress` of the `address` field. The static companion `normalizePostalAddress()` splits a `"street;extension;post-office box"` address into three fields. |
| `SetContactPointTrait` | `defaultTelephone`, `defaultEmail`, `defaultFaxNumber`, `homeTelephone`, `mobile`, `mobileProfessional`, … | The `contactPoint` collection: one `ContactPoint` per usage (`ContactType`: default, home, mobile…), merged when the usage already exists. Invalid phone numbers and malformed emails are discarded. |
| `SetGeoCoordinatesTrait` | `geoLatitude`, `geoLongitude`, `geoElevation`, `geoDistance` | The `GeoCoordinates` of the `geo` field. |
| `SetProductProviderInfoTrait` | `buyingPrice`, `buyingPriceMargin`, `buyingPriceReferenceQuantity`, `nextBuyingPrice`, … | The `ProductProviderInfo` of a supplier (the `productInfo` field). |
| `SetAdditionalPropertyTrait` (org) | — | The low-level injector: appends a `PropertyValue` to `additionalProperty`. Each class specializes it with its own allowed-key list and normalization (`CustomerAdditionalProperty`, `PersonAdditionalProperty`, `ProductAdditionalProperty`, `SiteAdditionalProperty` — boolean and integer coercions). |
| `SiteTrait` | the aggregate | Composes address + contacts + geolocation + additional properties for the places (`Place`, `CustomerSite`, `ProviderSite`). |
| `UnitPriceSpecificationTrait` | — | `getLastUnitPriceSpecification()` — the last unit-price specification of a collection. |
| `ProductProperty` | — | The descriptive attributes of a product (essence, appearance, certification, colors, …), carried as-is. |

## Who composes what

| Class | Composed ingestion traits |
|---|---|
| `Company` (and its flavors) | `SetPostalAddressTrait`, `SetContactPointTrait`, additional properties |
| `Person` (and its flavors) | `SetContactPointTrait`, additional properties |
| `Place` / `CustomerSite` / `ProviderSite` | `SiteTrait` (the full aggregate) |
| `Provider` | + `SetProductProviderInfoTrait` |
| `Product` | additional properties + the `eligibleQuantity` tree (see [Products](products.md)) |

---

## Ingestion vs hydrators: two different moments

The ingestion traits work **on the way in**, when the data arrives flat (key by key, through `__set`). The [`hydrate*` helper functions](helpers.md) work **on the way back**, when the data returns already structured but untyped (a full document to retype in one go). The two complement each other: ingestion writes, hydration re-reads.

---

## See also

- [Organizations](organizations.md), [People](people.md), [Products](products.md), [Places](places.md) — the classes composing these traits.
- [Helper functions](helpers.md) — the typed re-reading of documents.
