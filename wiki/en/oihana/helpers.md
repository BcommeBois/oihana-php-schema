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

| Namespace                                        | Content                                                                    | Depends on         |
|--------------------------------------------------|----------------------------------------------------------------------------|--------------------|
| `org\schema\helpers\hydrate`                     | The 9 pure Schema.org hydrators, plus `findEnumerationMember`               | `org\schema` only  |
| `xyz\oihana\schema\helpers\hydrate`              | The 10 business-layer hydrators, plus `findPhysicalQuantityByType` and `termClassOf` | `xyz` + `org`      |
| `xyz\oihana\schema\helpers\hydrate\appointments` | The 4 appointment hydrators                                                 | `xyz` + `org`      |
| `xyz\oihana\schema\helpers\hydrate\documents`    | The 5 document hydrators                                                    | `xyz` + `org`      |
| `xyz\oihana\schema\helpers\pivots`               | The 4 account pivots                                                        | `xyz` + `org`      |

The business hydrators delegate their nested references to the pure ones (`hydrateCustomer` calls `hydrateContactPoint` and `hydratePostalAddress`) — the arrow always points `xyz` → `org`.

The `hydrate/documents` subfolder gathers the hydrators of the [business documents](business-documents.md). They differ from the others on one point: instead of calling the constructor and then re-wiring every nested reference by hand, they go through `Reflection::hydrate()` — the only path that honors the `#[HydrateAs]` / `#[HydrateWith]` attributes already carried by `BusinessDocumentLine`/`BusinessDocument`. The mapping therefore stays declared once, on the class.

`hydrateOrganizationOrPerson`, on the other hand, lives in `org\schema\helpers\hydrate`: since it only needs `org\schema\Organization`/`org\schema\Person`, it stays a pure hydrator. It resolves an `Organization|Person` union from the payload's `@type`, and accepts two custom target classes (`$organizationClass`/`$personClass`) to aim at a business subtype.

`hydrateParcelDelivery` is the counter-example that shows best what this layering is for. A document's delivery is an `org\schema\ParcelDelivery`, but its method and its round are **business** thesaurus terms (`DeliveryMethodTerm`, `DeliveryRouteTerm`). Naming them in a `#[HydrateAs]` carried by `ParcelDelivery` would have reversed the arrow: the Schema.org class would have started depending on the business layer. The hydrator therefore takes them as **parameters** (`class-string<DefinedTerm>`, defaulting to the business terms), and lives on the `xyz` side where that knowledge is allowed to exist. It is also why it is the only one of the document family to go through the constructor rather than `Reflection::hydrate()`: with no attribute to honor on those three properties, reflection would bring nothing — and its strictness would drop, on the way, whatever a stored delivery carries beyond the Schema.org vocabulary.

**What `hydrateParcelDelivery` started has become a pattern.** The appointment hydrators take the same kind of parameter: `hydrateVisitReport` and `hydrateCustomerAppointment` accept a `$termClass` saying which class their vocabularies are read back into — `ThesaurusTerm` by default, the class the business families actually serve. Without it, a term silently lost whatever `DefinedTerm` does not declare, `color` first among them: a constructor only assigns the properties its class declares, and the key fell without an error and without a trace. The same term then changed shape depending on the door it was read through — its own family, or the report quoting it.

Where the delivery names two parameters (`$deliveryMethodClass`, `$deliveryRouteClass`), a meeting carries four vocabularies and its report four more. Hence **one parameter with two forms**, read by `termClassOf`:

```php
hydrateCustomerAppointment( $raw ) ;                      // the house term, everywhere
hydrateCustomerAppointment( $raw , DefinedTerm::class ) ; // one named class, everywhere

hydrateCustomerAppointment( $raw ,
[
    Prop::DEFAULT                         => ThesaurusTerm::class ,
    CustomerAppointment::APPOINTMENT_TYPE => AppointmentTypeTerm::class ,
    CustomerAppointment::REPORT           => [ VisitReport::MOOD => MoodTerm::class ] ,
]) ;
```

The two forms are the same statement at two levels of detail: a caller only writes a map the day one family stops answering what the others answer.

⚠️ **A map is keyed by property, not by family.** `tags` is declared on the meeting **and** on its report, over two different families — a meeting's quick qualifiers are not the qualifiers of the text written about it. One key named `tags` cannot tell them apart: the `report` branch is what separates them. Without it, the report **inherits the meeting's map**, which is right as long as the two families agree, and is what the branch exists to undo the day they diverge.

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
hydrateCustomer( [ 'raw' , [ 'name' => 'A' ] ] ) ;            // a mixed list   → [ 'raw' , Customer ]
```

### The rule for nested references

A hydrator that resolves nested references (`hydrateCustomer`, `hydrateCustomerSite`,
`hydrateBusinessDocument`, …) applies one rule to each of them:

- **it hydrates only what is an array** — there is something to hydrate. An unresolved
  string reference or an already typed instance is left untouched, never rewritten.
  🔑 **This holds entry by entry inside a list**: a list of handles — published codes,
  vocabulary identifiers — comes back as it stands, and the keys stay gap-free, a list
  left with holes serializing as a JSON **object**;
- **the nested hydrator's answer is then written as is, `null` included.** An array that
  resolves to nothing — an empty list, a list of entries that **were arrays** and gave
  nothing — becomes `null`, never a leftover raw array;
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

#### The exception: a document's lines and adjustments

Two hydrators escape that `null` — but **on their top-level argument only**, never on the references they resolve underneath. `hydrateDocumentLine()` and `hydrateAdjustment()` return an **empty list as it came**:

```php
hydrateDocumentLine( [] ) ;   // []  — "this document has no line"
hydrateAdjustment  ( [] ) ;   // []  — "this document has no adjustment"

hydrateDocumentLine( [ null ] ) ;  // null — nothing was readable: not the same answer
hydrateDocumentLine( [ 'raw' ] ) ; // [ 'raw' ] — a handle is not an unreadable line, it is a reference
```

The lines and the adjustments are the two places where "there are none" is an answer worth serving: a draft is commonly born without a single line, and a `null` makes the key vanish from the serialized shape — a consumer walking the value then falls on an absence instead of the empty list it could walk.

It is also what **makes the two paths agree** here: through reflection, an empty `documentLines` stays `[]` (the `#[HydrateWith]` attribute walks an empty list and returns an empty list). Before, the hydrator called on its own answered `null` where the parent answered `[]`.

No other hydrator changes: an empty list handed to `hydrateCustomer`, `hydrateCustomerSite`, `hydrateDeliveryRouteAssignment` — or found in a nested reference, whichever it is — still answers `null`.

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

## Quick example — hydrating the rest of the header

The same need arises, slot by slot, when the document was built elsewhere: its amounts, its adjustments and its delivery are then raw arrays.

```php
use function xyz\oihana\schema\helpers\hydrate\documents\hydrateAdjustment;
use function xyz\oihana\schema\helpers\hydrate\documents\hydrateDocumentTotals;
use function xyz\oihana\schema\helpers\hydrate\hydrateParcelDelivery;

$document->totals        = hydrateDocumentTotals( $document->totals        ) ;
$document->adjustments   = hydrateAdjustment    ( $document->adjustments   ) ;
$document->orderDelivery = hydrateParcelDelivery( $document->orderDelivery ) ;

$document->totals->total->value ;                          // 62.4  (MonetaryAmount)
$document->adjustments[0]->amount->value ;                 // 52.0  (MonetaryAmount)
$document->adjustments[0]->taxes[0]->taxAmount->value ;    // 10.4  (MonetaryAmount)
$document->orderDelivery->deliveryAddress->postalCode ;    // '33270' (PostalAddress)
$document->orderDelivery->hasDeliveryMethod->id ;          // 'F13'   (DeliveryMethodTerm)
```

The first two go through `Reflection::hydrate()`: `DocumentTotals`, `Adjustment` and `TaxDetail` all declare their amounts with a `#[HydrateAs(MonetaryAmount::class)]`, and reflection **recurses** — so a single call on an adjustment types its `amount`, its `taxes`, and the `basisAmount`/`taxAmount` each `TaxDetail` declares in its turn. Nothing had to be added for `TaxDetail` or `MonetaryAmount`: the attributes were already there, only the path that reads them was missing.

The third takes the other route, for the layering reason given [above](#namespace-layering).

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
| `findEnumerationMember`     | the member class of an enumeration | Builds nothing : **reads** from a raw payload the `additionalType` (the URI each member states), then failing that the `@type` (the short class name), and answers the class to hydrate into — the enumeration head when nothing is recognized, so an unknown status keeps the reason it carried. Shared by both status helpers, the two vocabularies asking the same question. |
| `hydrateAdditionalProperty` | `PropertyValue[]`                | indexed list only, `null` otherwise    |
| `hydrateContactPoint`       | `ContactPoint[]`                 | indexed list only, `null` otherwise    |
| `hydrateDefinedTerm`        | `DefinedTerm` or `DefinedTerm[]` | single, list, passthrough — target class overridable via `$class` (e.g. `DeliveryMethodTerm::class`) |
| `hydrateEventStatus`        | the member class of `EventStatusType` | single, list, passthrough — **the bare constant comes back untouched**, an array becomes the object carrying its reason again (`EventCancelled` and its `description`). Member resolved by `findEnumerationMember`. |
| `hydrateGeoCoordinates`     | `GeoCoordinates` or list         | single, list, passthrough              |
| `hydrateOffer`              | a **bare** `Offer`, or list      | single, list, passthrough — `eligibleQuantity` and `priceSpecification` come from `Reflection::hydrate()`, `itemOffered` is settled by the payload's `@type` (a `Service` suffix → `Service`, otherwise `$productClass`). 🔑 `$productClass` is what keeps the helper in `org\schema` : the commerce-enriched `Product` lives in `xyz`, so it is passed in. Serves `Organization::$makesOffer` and every property carrying a list of bare offers. |
| `hydrateOfferPurchase`      | `OfferForPurchase`               | array or instance, `null` otherwise — types the `eligibleCustomerType` as `BusinessEntityType` |
| `hydrateOrganizationOrPerson` | `Organization` or `Person`, or list | Resolves the union from the `@type`: `Person` → `Person`, otherwise `Organization` (the safe default) — target classes overridable via `$organizationClass`/`$personClass` |
| `hydratePostalAddress`      | `PostalAddress` or list          | single (empty values cleaned), list, passthrough |

### `xyz\oihana\schema\helpers\hydrate` — the business hydrators

| Function                  | Produces            | Hydrated nested references                               |
|---------------------------|---------------------|----------------------------------------------------------|
| `findPhysicalQuantityByType` | `PhysicalQuantity` or `null` | Builds nothing nested: **walks** a packaging chain — handed in as a parameter, typed or raw — and returns the level whose `additionalType` matches, weight and volume included — **the chain below it typed as well**, so a weight reads `->weight` at any depth. The tree is a parameter because it is not always reachable from the product that defined it: it is copied onto the offers, and it is that copy which is stored. A `valueReference` that is not a level — a bare code, an enumeration, both of which Schema.org allows — ends the walk. `Product::findEligibleQuantityByType()` is now that call on its own tree. |
| `hydrateAggregateOffer`   | `AggregateOffer`    | `availableAtOrFrom` (Warehouse), `eligibleQuantity` (through `hydratePhysicalQuantity`), `offers` (OfferForPurchase[]), `provider` |
| `hydrateCustomer`         | `Customer` or list  | `contactPoint`, `address`                                |
| `hydrateCustomerEmployee` | `CustomerEmployee` or list | `additionalProperty`, `contactPoint`, `workLocation` (CustomerSite) |
| `hydrateCustomerSite`     | `CustomerSite` or list | `additionalProperty`, `address`, `geo`, `deliveryMethod` (DeliveryMethodTerm), `deliveryRoute` (DeliveryRouteAssignment[]) |
| `hydrateDeliveryRouteAssignment` | `DeliveryRouteAssignment` or list | `route` (DeliveryRouteTerm, when the joined reference row is present — a bare code is left alone) |
| `hydrateFeeSpecification` | `FeeSpecification`  | `rate` (UnitPriceSpecification — a rate already typed is left alone rather than rebuilt), `publisher` (resolved by `hydrateOrganizationOrPerson`). Accepts an instance as well as an array, and completes it in place. For the **constructor** path only: `Reflection::hydrate()` types both on its own, the attribute being declared on `FeeSpecification::$rate`. |
| `hydrateParcelDelivery`   | `ParcelDelivery` or list | `deliveryAddress` and `originAddress` (PostalAddress), `hasDeliveryMethod` and `hasDeliveryRoute` (target classes customizable through `$deliveryMethodClass`/`$deliveryRouteClass`, defaulting to `DeliveryMethodTerm`/`DeliveryRouteTerm`), `provider` (resolved by `hydrateOrganizationOrPerson`) |
| `hydratePhysicalQuantity` | `PhysicalQuantity`  | `valueReference` — **recursively**, every packaging level keeping its `weight` and its `volume`. A level already typed is handed back as it stands. For the **constructor** path only: `Reflection::hydrate()` walks the chain on its own, the attribute being declared on the property. |
| `hydrateStockLevel`       | `StockLevel`        | `assignedPOS` (Warehouse)                                |
| `hydrateWarehouse`        | `Warehouse` or list | `ownedBy` (Subsidiary)                                   |
| `termClassOf`             | a `class-string<DefinedTerm>` | Builds nothing: **answers the class** one term property is read back into. Reads both forms of the `$termClass` parameter — a class name, or a map `[ Prop::DEFAULT => …, '<property>' => … ]`. What a map does not name falls back on `Prop::DEFAULT`, then on `ThesaurusTerm`. 🔑 A **nested branch** — a sub-map, such as `report` — is not a class name and is never answered as one: it belongs to the hydrator owning that property, which reads it and hands it down itself. |

### `xyz\oihana\schema\helpers\hydrate\appointments` — the appointment hydrators

| Function                    | Produces                          | Nested references hydrated                               |
|-----------------------------|-----------------------------------|----------------------------------------------------------|
| `hydrateAppointmentStatus` | the member class of `AppointmentStatus` | Goes nowhere down : **the bare constant comes back untouched**, an array becomes the object carrying its reason again. The twin of `hydrateEventStatus` on the other axis — what became of the meeting, not of the slot. ⚠️ The vocabulary spells `…/AppointmentStatus#NoShow` where the class is named `AppointmentNoShow` : no rule takes one to the other, so the URI is **stated** by the member. |
| `hydrateCustomerAppointment` | `CustomerAppointment` or list | `customer`, `attendee` (CustomerEmployee[] and their own references), `assignedSeller` (Seller), `appointmentType` (one term) and `tags` (several) — **read back as `ThesaurusTerm`**, the target class being customizable through `$termClass` (a class name, or a per-property map read by `termClassOf`, `report` branch included) —, `makesOffer` (through `hydrateOffer`, given this package's `Product`), `report` (through `hydrateVisitReport`), `eventStatus` and `appointmentStatus`. The rest comes from `Reflection::hydrate()`. 🔑 `organizer`, `assignedCompany` and `location` are **left to their attribute**, which reflection already settles exactly from the `@type` : forcing a class over them would read a plain organization back as a subsidiary, a virtual room as a customer site. |
| `hydrateFollowUp`         | `FollowUp` or list               | `followUpType`, `agent` (resolved by `hydrateOrganizationOrPerson`), `result` — 🚨 **flat, never the deep helper** : the meeting named as the result is a reference, and going down would open a cycle only the data would stop. **An empty list is kept as an empty list.** |
| `hydrateVisitReport`      | `VisitReport` or list            | `attendee` (CustomerEmployee[]), `followUp` (FollowUp[], an empty list kept), `mood` and `outcome` (one term), `tags` and `topics` (several) — all four **read back as `ThesaurusTerm`**, the target class being customizable through `$termClass` (a class name, or a per-property map read by `termClassOf`) —, `author` (resolved by `hydrateOrganizationOrPerson`) |

### `xyz\oihana\schema\helpers\hydrate\documents` — the document hydrators

| Function                  | Produces                         | Hydrated nested references                               |
|---------------------------|----------------------------------|----------------------------------------------------------|
| `hydrateAdjustment`       | `Adjustment` or list              | `amount` (MonetaryAmount), `taxes` (TaxDetail[], with their own `basisAmount`/`taxAmount`) — **an empty list is returned as it came** |
| `hydrateBusinessDocument` | `BusinessDocument` (or a subclass via `$class`), or list | `customer`, `seller`, `author` (plus `broker`/`provider` on `Invoice`), resolved by `hydrateOrganizationOrPerson` — everything else (`documentLines`, `taxes`, `totals`...) comes from `Reflection::hydrate()` |
| `hydrateDocumentLine`     | `BusinessDocumentLine` or list    | `adjustments` (Adjustment[]), `price` (CompoundPriceSpecification + its `priceComponent`), `quantity`, `subtotal`, `taxes` (TaxDetail[]), `total`, `item` (delegated below) — **an empty list is returned as it came** |
| `hydrateDocumentLineItem` | `Product` or `Service`, or list   | Resolves the union from the `@type`: a `Service` suffix → `Service`, otherwise `Product` (with its `eligibleQuantity` and `inventoryLevel`) |
| `hydrateDocumentTotals`   | `DocumentTotals` or list          | The seven declared amounts (`allowanceTotal`, `balanceDue`, `chargeTotal`, `prepaidAmount`, `subtotal`, `total`, `totalTax`) as `MonetaryAmount` — an empty list answers `null`, absent totals being said with an absent value |

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
- [Appointments](appointments.md) — `CustomerAppointment`, `VisitReport`, `FollowUp` and the « reading an appointment back » section.
- [Schema.org vocabulary](../schema-org/README.md) — the classes produced by the pure hydrators.
