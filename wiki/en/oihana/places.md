# `xyz\oihana\schema\places` — Oihana places

The `xyz\oihana\schema\places` namespace adds **operational, business-oriented place types** that are not part of Schema.org core. They model real-world locations within an organization (sites, offices, warehouses, jobsites) while staying compatible with the standard `Place` vocabulary.

> 🇫🇷 Cette page existe aussi en [français](../../fr/oihana/places.md).

---

## When to use it

Pick these classes whenever you need to describe **where business happens** with richer semantics than a generic `Place`:

- a *Site* with delivery methods and an owning organization,
- an *Office* or *Warehouse* used by an organization,
- a temporary *JobSite* tied to a project.

Every class extends `org\schema\Place`, so it inherits all the address, geo, telephone and `sameAs` properties of Schema.org, while exposing the `@context = 'https://schema.oihana.xyz'` distinguisher in the JSON-LD output.

---

## Quick example

```php
use org\schema\PostalAddress;
use org\schema\Organization;
use xyz\oihana\schema\places\Site;

$site = new Site
([
    'name'    => 'Main warehouse' ,
    'address' => new PostalAddress
    ([
        'streetAddress'   => '2 chemin des Vergers' ,
        'postalCode'      => '49170' ,
        'addressLocality' => 'Saint-Georges-sur-Loire' ,
    ]),
    'ownedBy'       => new Organization([ 'name' => 'Oihana SAS' ]) ,
    'deliveryMethod' => 'OnSitePickup' ,
    'position'       => 1 ,
]);

echo json_encode( $site , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

---

## Class catalog

| Class       | Extends   | Purpose                                                                                            |
|-------------|-----------|----------------------------------------------------------------------------------------------------|
| `Site`      | `Place`   | Multi-functional operational site (customer site, provider site, industrial location). Adds `deliveryMethod`, `ownedBy`, `position`. |
| `Office`    | `Place`   | Office building or floor used by an organization.                                                  |
| `Warehouse` | `Place`   | Storage facility — typically with delivery methods, capacity and operating hours.                   |
| `JobSite`   | `Place`   | Project-tied location (construction, on-site intervention, temporary deployment).                   |

For exhaustive property lists, browse the source under [`src/xyz/oihana/schema/places/`](../../src/xyz/oihana/schema/places) or the [API reference](../../../docs).

---

## Related constants

Site-specific property keys are exposed by the [`SiteTrait`](../../src/xyz/oihana/schema/constants/traits/places/SiteTrait.php) trait, composed into the master [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php) aggregator. You can therefore reach them through `Oihana::DELIVERY_METHOD`, `Oihana::OWNED_BY`, `Oihana::POSITION`, etc.

---

## Related reading

- [`org\schema`](../schema-org/README.md) — `Place`, `PostalAddress`, `Organization`, `Person`.
- [Getting started](../getting-started.md) — installation, hydration, JSON-LD basics.
- [API reference](../../../docs).
