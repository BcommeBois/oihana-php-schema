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

| Namespace                                     | Content                         | Depends on         |
|-----------------------------------------------|---------------------------------|--------------------|
| `org\schema\helpers\hydrate`                  | The 7 pure Schema.org hydrators | `org\schema` only  |
| `xyz\oihana\schema\helpers\hydrate`           | The 6 business-layer hydrators  | `xyz` + `org`      |
| `xyz\oihana\schema\helpers\hydrate\documents` | The 3 document hydrators        | `xyz` + `org`      |
| `xyz\oihana\schema\helpers\pivots`            | The 3 account pivots            | `xyz` + `org`      |

The business hydrators delegate their nested references to the pure ones (`hydrateCustomer` calls `hydrateContactPoint` and `hydratePostalAddress`) — the arrow always points `xyz` → `org`.

The `hydrate/documents` subfolder gathers the hydrators of the [business documents](business-documents.md). They differ from the others on one point: instead of calling the constructor and then re-wiring every nested reference by hand, they go through `Reflection::hydrate()` — the only path that honors the `#[HydrateAs]` / `#[HydrateWith]` attributes already carried by `BusinessDocumentLine`/`BusinessDocument`. The mapping therefore stays declared once, on the class.

`hydrateOrganizationOrPerson`, on the other hand, lives in `org\schema\helpers\hydrate`: since it only needs `org\schema\Organization`/`org\schema\Person`, it stays a pure hydrator. It resolves an `Organization|Person` union from the payload's `@type`, and accepts two custom target classes (`$organizationClass`/`$personClass`) to aim at a business subtype.

> **Note.** The documents' ambiguous unions (`customer`/`seller`/`author`, `broker`/`provider`, `item`, `ownedBy`) are now settled **declaratively**, through a `#[HydrateWith(A::class, B::class)]` carried by the property: `Reflection::hydrate()` then picks the right class from the discriminator (`@type`, `atType` or `type`) and, failing that, from the properties present. Resolution is therefore correct **even without going through a helper**. The hydrators of this layer stay useful for what reflection does not do: accepting a single definition, an indexed list, or any other value returned unchanged.

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

### The rule for nested references

A hydrator that resolves nested references (`hydrateCustomer`, `hydrateCustomerSite`,
`hydrateBusinessDocument`, …) applies one rule to each of them:

- **it hydrates only what is an array** — there is something to hydrate. An unresolved
  string reference or an already typed instance is left untouched, never rewritten;
- **the nested hydrator's answer is then written as is, `null` included.** An array that
  resolves to nothing — an empty list, a list of unhydratable entries — becomes `null`,
  never a leftover raw array;
- **a property the payload never carried is not invented.** Hydration leaves it in
  whatever state its declaration gives it: declared without a default, it stays
  uninitialized and so absent from the serialized shape; declared `= null`, it stays
  `null` and is serialized as such. Either way hydration does not touch it.

The second point is what makes the two paths agree: a nested reference answers the same
thing through its parent as through the nested hydrator called on its own.

```php
hydrateDeliveryRouteAssignment( [] ) ;                                // null

$site = hydrateCustomerSite( [ 'name' => 'A' , 'deliveryRoute' => [] ] ) ;
$site->deliveryRoute ;                                                // null — the same answer
```

---

## Quick example — hydrating a document's lines

```php
use function xyz\oihana\schema\helpers\hydrate\documents\hydrateDocumentLine;

// $document: a BusinessDocument coming out of the server — its lines are raw arrays.

$document->documentLines = hydrateDocumentLine( $document->documentLines ) ;

$line = $document->documentLines[0] ;

$line->quantity->value ;                // 5       (QuantitativeValue)
$line->price->price ;                   // 22.5    (CompoundPriceSpecification)
$line->price->priceComponent[0]->name ; // 'base'  (UnitPriceSpecification)
$line->taxes[0]->rate ;                 // 20.0    (TaxDetail)
$line->total->value ;                   // 135.0   (MonetaryAmount)
$line->item->name ;                     // 'White paint' (Product)
```

The `BusinessDocument` constructor only performs a shallow assignment: without that call, `documentLines` stays an array of arrays, and a line's price, quantity, taxes and totals never become objects.

A line's `item` is a `Product|Service` union: the payload's `@type` decides its class — `Service` for a service, the commerce-enriched `Product` otherwise — thanks to the `#[HydrateWith(Product::class, Service::class)]` carried by the property.

---

## Quick example — hydrating a whole document

```php
use function xyz\oihana\schema\helpers\hydrate\documents\hydrateBusinessDocument;
use xyz\oihana\schema\business\documents\Invoice;

// $raw: the server's raw response for an invoice.

$invoice = hydrateBusinessDocument( $raw , Invoice::class ) ;

$invoice->customer->name ; // 'Jean Dupont' — Person, even though Organization comes first in the union
$invoice->seller->name   ; // 'ACME'        — Organization
$invoice->provider->name ; // 'Sous-traitant SA' — Organization, even without an explicit @type
```

`customer`, `seller` and `author` are typed `Organization|Person` on every `BusinessDocument` (plus `broker`/`provider` on `Invoice`) — the same ambiguity as a line's `item`, at the header level, and settled the same way by a `#[HydrateWith(Organization::class, Person::class)]`. `hydrateBusinessDocument()` hydrates the document through `Reflection::hydrate()` — so everything, header and lines alike, comes out typed — then goes over those properties again through `hydrateOrganizationOrPerson()`. Its second parameter (`$class`, defaulting to `BusinessDocument::class`) serves any link in the cycle — `Quote::class`, `PurchaseOrder::class`, `CreditNote::class`... — since none of them override `customer`/`seller`/`author`.

The helper's value is therefore not the union resolution — the attribute handles that, even for a direct `Reflection::hydrate()` call — but the three input shapes it accepts: one document, a list of documents, or any other value returned unchanged.

---

## Quick example — the account pivots

```php
use function xyz\oihana\schema\helpers\pivots\customerKey;
use function xyz\oihana\schema\helpers\pivots\customerKeys;
use function xyz\oihana\schema\helpers\pivots\sellerKeys;

// $user: a xyz\oihana\schema\auth\User with its `identities` hydrated.

$key      = customerKey( $user )  ; // '137285125' — the customer the contact works for, or null
$clients  = customerKeys( $user ) ; // [ '137285125' , '137285130' ] — all its customers, deduplicated
$keys     = sellerKeys( $user )   ; // [ '147737218' , '147737209' ] — the seller hats, deduplicated
```

An account carries zero, one or several business identities (see [`BusinessIdentity`](business.md)): `customerKey()` and `sellerKey()` resolve the first identity of the expected type; `customerKeys()` and `sellerKeys()` resolve them all.

---

## Function catalog

### `org\schema\helpers\hydrate` — the pure hydrators

| Function                    | Produces                         | Accepted shapes                        |
|-----------------------------|----------------------------------|----------------------------------------|
| `hydrateAdditionalProperty` | `PropertyValue[]`                | indexed list only, `null` otherwise    |
| `hydrateContactPoint`       | `ContactPoint[]`                 | indexed list only, `null` otherwise    |
| `hydrateDefinedTerm`        | `DefinedTerm` or `DefinedTerm[]` | single, list, passthrough — target class overridable via `$class` (e.g. `DeliveryMethodTerm::class`) |
| `hydrateGeoCoordinates`     | `GeoCoordinates` or list         | single, list, passthrough              |
| `hydrateOfferPurchase`      | `OfferForPurchase`               | array or instance, `null` otherwise — types the `eligibleCustomerType` as `BusinessEntityType` |
| `hydrateOrganizationOrPerson` | `Organization` or `Person`, or list | Resolves the union from the `@type`: `Person` → `Person`, otherwise `Organization` (the safe default) — target classes overridable via `$organizationClass`/`$personClass` |
| `hydratePostalAddress`      | `PostalAddress` or list          | single (empty values cleaned), list, passthrough |

### `xyz\oihana\schema\helpers\hydrate` — the business hydrators

| Function                  | Produces            | Hydrated nested references                               |
|---------------------------|---------------------|----------------------------------------------------------|
| `hydrateAggregateOffer`   | `AggregateOffer`    | `availableAtOrFrom` (Warehouse), `eligibleQuantity`, `offers` (OfferForPurchase[]), `provider` |
| `hydrateCustomer`         | `Customer` or list  | `contactPoint`, `address`                                |
| `hydrateCustomerEmployee` | `CustomerEmployee` or list | `additionalProperty`, `contactPoint`, `workLocation` (CustomerSite) |
| `hydrateCustomerSite`     | `CustomerSite` or list | `additionalProperty`, `address`, `geo`, `deliveryMethod` (DeliveryMethodTerm), `deliveryRoute` (DeliveryRouteAssignment[]) |
| `hydrateDeliveryRouteAssignment` | `DeliveryRouteAssignment` or list | `route` (DeliveryRouteTerm, when the joined reference row is present — a bare code is left alone) |
| `hydrateStockLevel`       | `StockLevel`        | `assignedPOS` (Warehouse)                                |
| `hydrateWarehouse`        | `Warehouse` or list | `ownedBy` (Subsidiary)                                   |

### `xyz\oihana\schema\helpers\hydrate\documents` — the document hydrators

| Function                  | Produces                         | Hydrated nested references                               |
|---------------------------|----------------------------------|----------------------------------------------------------|
| `hydrateBusinessDocument` | `BusinessDocument` (or a subclass via `$class`), or list | `customer`, `seller`, `author` (plus `broker`/`provider` on `Invoice`), resolved by `hydrateOrganizationOrPerson` — everything else (`documentLines`, `taxes`, `totals`...) comes from `Reflection::hydrate()` |
| `hydrateDocumentLine`     | `BusinessDocumentLine` or list    | `adjustments` (Adjustment[]), `price` (CompoundPriceSpecification + its `priceComponent`), `quantity`, `subtotal`, `taxes` (TaxDetail[]), `total`, `item` (delegated below) |
| `hydrateDocumentLineItem` | `Product` or `Service`, or list   | Resolves the union from the `@type`: a `Service` suffix → `Service`, otherwise `Product` (with its `eligibleQuantity` and `inventoryLevel`) |

### `xyz\oihana\schema\helpers\pivots` — the account pivots

| Function      | Returns             | Role                                                                 |
|---------------|---------------------|----------------------------------------------------------------------|
| `customerKey` | `_key` or `null`    | The customer organization the account's first contact identity works for (`worksFor`). |
| `customerKeys`| list of `_key`      | Every customer organization the account is a contact for, deduplicated, never `null` entries. |
| `sellerKey`   | `_key` or `null`    | The key of the account's first seller identity.                      |
| `sellerKeys`  | list of `_key`      | Every seller key of the account, deduplicated, never `null` entries. |

---

## See also

- [Oihana business](business.md) — `BusinessIdentity`, the account ↔ entity link the pivots walk through.
- [Business documents](business-documents.md) — `BusinessDocument`, `BusinessDocumentLine` and the value objects the document hydrators produce.
- [Schema.org vocabulary](../schema-org/README.md) — the classes produced by the pure hydrators.
