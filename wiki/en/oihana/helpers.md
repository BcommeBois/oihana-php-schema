# Helper functions — hydration and pivots

The library ships a layer of **helper functions**: free functions, autoloaded by Composer (the `autoload.files` section), that accompany the schema classes without adding methods to them. A helper is consumed through a plain `use function` — no instance, no state, one input, one output.

Two families live in this layer:

- the **hydrators** — turn a raw piece of data (an associative array coming out of a database or an API) into a typed schema object, nested references included;
- the **account pivots** — resolve the business identities of an authenticated account (`User`) into the organization or seller keys that scope its resources.

> 🇫🇷 Cette page est aussi disponible en [français](../../fr/oihana/helpers.md).

---

## When to use it

Reach for a hydrator when a piece of data arrives **already structured but untyped** — a document projected by a query, a decoded JSON response — and you want to handle it as a schema object without hand-wiring the nested references:

- `hydrateCustomer( $document )` returns a `Customer` whose `contactPoint` entries are `ContactPoint` objects and whose `address` is a `PostalAddress`;
- every hydrator accepts **a single definition**, **an indexed list** of definitions (invalid entries are filtered out), or **any other value**, returned unchanged (passthrough).

Reach for a pivot when an authenticated account must be reduced to **the key that scopes its perimeter**: the customer a contact works for, the seller hat(s) a salesperson wears.

---

## Loading

The functions are registered in the `autoload.files` section of the library's `composer.json`: they are available everywhere, without instantiation. The import follows the function's namespace:

```php
use function org\schema\helpers\hydrate\hydratePostalAddress;
use function xyz\oihana\schema\helpers\hydrate\hydrateCustomer;
use function xyz\oihana\schema\helpers\pivots\sellerKeys;
```

---

## Namespace layering

The layer follows the library's rule: `org\schema` is the pure mirror of the Schema.org vocabulary, `xyz\oihana\schema` is the house extension built on top of it — **never the other way around**.

| Namespace                              | Content                                | Depends on         |
|----------------------------------------|----------------------------------------|--------------------|
| `org\schema\helpers\hydrate`           | The 6 pure Schema.org hydrators        | `org\schema` only  |
| `xyz\oihana\schema\helpers\hydrate`    | The 6 business-layer hydrators         | `xyz` + `org`      |
| `xyz\oihana\schema\helpers\pivots`     | The 3 account pivots                   | `xyz` + `org`      |

The business hydrators delegate their nested references to the pure ones (`hydrateCustomer` calls `hydrateContactPoint` and `hydratePostalAddress`) — the arrow always points `xyz` → `org`.

---

## Quick example — hydrating a customer

```php
use function xyz\oihana\schema\helpers\hydrate\hydrateCustomer;

$customer = hydrateCustomer
([
    'name'         => 'South Wood Company' ,
    'contactPoint' => [ [ 'telephone' => '05 59 00 00 00' ] ] ,
    'address'      => [ 'streetAddress' => '20 Rue Mably' , 'postalCode' => '33000' ] ,
]);

$customer->name ;                        // 'South Wood Company'
$customer->contactPoint[0]->telephone ;  // '05 59 00 00 00'  (ContactPoint)
$customer->address->streetAddress ;      // '20 Rue Mably'    (PostalAddress)
```

The three shapes accepted by the thing hydrators (`hydrateCustomer`, `hydrateWarehouse`, `hydrateDefinedTerm`, …):

```php
hydrateCustomer( [ 'name' => 'A' ] ) ;                        // one definition → Customer
hydrateCustomer( [ [ 'name' => 'A' ] , [ 'name' => 'B' ] ] ); // a list         → Customer[]
hydrateCustomer( 'raw' ) ;                                    // anything else  → returned unchanged
```

---

## Quick example — the account pivots

```php
use function xyz\oihana\schema\helpers\pivots\customerKey;
use function xyz\oihana\schema\helpers\pivots\sellerKeys;

// $user: a xyz\oihana\schema\auth\User with its `identities` hydrated.

$key  = customerKey( $user ) ; // '137285125' — the customer the contact works for, or null
$keys = sellerKeys( $user ) ;  // [ '147737218' , '147737209' ] — the seller hats, deduplicated
```

An account carries zero, one or several business identities (see [`BusinessIdentity`](business.md)): `customerKey()` and `sellerKey()` resolve the first identity of the expected type, `sellerKeys()` resolves them all.

---

## Function catalog

### `org\schema\helpers\hydrate` — the pure hydrators

| Function                    | Produces                         | Accepted shapes                        |
|-----------------------------|----------------------------------|----------------------------------------|
| `hydrateAdditionalProperty` | `PropertyValue[]`                | indexed list only, `null` otherwise    |
| `hydrateContactPoint`       | `ContactPoint[]`                 | indexed list only, `null` otherwise    |
| `hydrateDefinedTerm`        | `DefinedTerm` or `DefinedTerm[]` | single, list, passthrough              |
| `hydrateGeoCoordinates`     | `GeoCoordinates` or list         | single, list, passthrough              |
| `hydrateOfferPurchase`      | `OfferForPurchase`               | array or instance, `null` otherwise — types the `eligibleCustomerType` as `BusinessEntityType` |
| `hydratePostalAddress`      | `PostalAddress` or list          | single (empty values cleaned), list, passthrough |

### `xyz\oihana\schema\helpers\hydrate` — the business hydrators

| Function                  | Produces            | Hydrated nested references                               |
|---------------------------|---------------------|----------------------------------------------------------|
| `hydrateAggregateOffer`   | `AggregateOffer`    | `availableAtOrFrom` (Warehouse), `eligibleQuantity`, `offers` (OfferForPurchase[]), `provider` |
| `hydrateCustomer`         | `Customer` or list  | `contactPoint`, `address`                                |
| `hydrateCustomerEmployee` | `CustomerEmployee` or list | `additionalProperty`, `contactPoint`, `workLocation` (CustomerSite) |
| `hydrateCustomerSite`     | `CustomerSite` or list | `additionalProperty`, `address`, `geo`, `deliveryMethod` |
| `hydrateStockLevel`       | `StockLevel`        | `assignedPOS` (Warehouse)                                |
| `hydrateWarehouse`        | `Warehouse` or list | `ownedBy` (Subsidiary)                                   |

### `xyz\oihana\schema\helpers\pivots` — the account pivots

| Function      | Returns             | Role                                                                 |
|---------------|---------------------|----------------------------------------------------------------------|
| `customerKey` | `_key` or `null`    | The customer organization the account's first contact identity works for (`worksFor`). |
| `sellerKey`   | `_key` or `null`    | The key of the account's first seller identity.                      |
| `sellerKeys`  | list of `_key`      | Every seller key of the account, deduplicated, never `null` entries. |

---

## See also

- [Oihana business](business.md) — `BusinessIdentity`, the account ↔ entity link the pivots walk through.
- [Schema.org vocabulary](../schema-org/README.md) — the classes produced by the pure hydrators.
