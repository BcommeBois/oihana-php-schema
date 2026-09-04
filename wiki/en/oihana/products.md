# `xyz\oihana\schema\products` — The commerce layer

The `xyz\oihana\schema\products` namespace carries the library's **commerce layer**: `Product`, the product enriched with its selling metadata (unit of sale, eligible quantities, pricing categories, VAT, stock), and its constellation of satellite classes — stock levels, price specifications, payment conditions and methods, provider and warehouse information.

The heart of the model is **the eligible-quantity tree**: the unit → package → pallet chain describing how a product is packaged, from which every unit-of-sale conversion derives.

> 🇫🇷 Cette page est aussi disponible en [français](../../fr/oihana/products.md).

---

## When to use it

Reach for `Product` as soon as an article carries a commercial dimension: a unit of sale, a packaging, a reference price, a stock level. The class extends the Schema.org `SomeProducts` — a product document stays standard JSON-LD, enriched with the house context.

---

## The eligible-quantity tree

A product sells by the unit, the package or the pallet (`unitOfSale`, values of the `UnitOfSaleType` enumeration). The `eligibleQuantity` tree describes the full chain: each level is a `PhysicalQuantity` (quantity, UN/CEFACT unit code, label, plus `weight` and `volume`) whose `valueReference` points to the upper level. `PhysicalQuantity` extends `QuantitativeValue`: anything typed on the mirror class — `Offer::$eligibleQuantity`, for one — accepts it unchanged, and a consumer reading only `value` and `unitCode` never notices the difference.

The tree **builds itself at hydration time**: the flat dataset keys (`eligibleUnitQuantityCode`, `eligiblePackageQuantityCode`, `eligiblePackageQuantityValue`, …) go through the class's magic `__set` and assemble the chain.

```php
use xyz\oihana\schema\products\Product;
use xyz\oihana\schema\enumerations\UnitOfSaleType;

$product = new Product() ;

$product->eligibleUnitQuantityCode     = 'C62' ;  // the unit
$product->eligiblePackageQuantityCode  = 'PA'  ;  // the package
$product->eligiblePackageQuantityValue = 12    ;  // 12 units per package

$product->unitOfSale = UnitOfSaleType::PACKAGE ;

$product->getUnitOfSaleConversionFactor() ;       // 12.0
```

### The conversions

| Method | Returns | Role |
|---|---|---|
| `getUnitOfSaleConversionFactor()` | `float` | The multiplication factor between the base unit and the unit of sale (1.0 for the unit). |
| `getInventoryLevelInUnitOfSale( $level )` | `float` or `null` | The stock converted into the unit of sale. |
| `findEligibleQuantityByType( $type )` | `PhysicalQuantity` or `null` | The tree level matching a `UnitOfSaleType`. |

### The weight and the volume of each level

A product sold by the package does not weigh what the same product weighs by the piece. Each level of the tree therefore carries **its own mass and its own bulk**, on the node they describe and never beside it:

```php
$package = $product->findEligibleQuantityByType( UnitOfSaleType::PACKAGE ) ;

$package->value  ;  // 1.403 — the quantity of the level
$package->weight ;  // 15.419
$package->volume ;  // 0.0312
```

Both properties accept a plain number when the unit is implicit, or a `QuantitativeValue` when it is stated (`{ "value": 15.419, "unitCode": "KGM" }`) — an array is hydrated as the latter.

### Filling them at hydration time

Each level has its **flat keys**, on the pattern of the codes and quantities — a dataset carrying them is all it takes for the tree to build itself with its measures:

| Level | Code | Quantity | Weight | Volume |
| :--- | :--- | :--- | :--- | :--- |
| unit | `eligibleUnitQuantityCode` | — | `eligibleUnitQuantityWeight` | `eligibleUnitQuantityVolume` |
| package | `eligiblePackageQuantityCode` | `eligiblePackageQuantityValue` | `eligiblePackageQuantityWeight` | `eligiblePackageQuantityVolume` |
| pallet | `eligiblePalletQuantityCode` | `eligiblePalletQuantityValue` | `eligiblePalletQuantityWeight` | `eligiblePalletQuantityVolume` |

```php
$product->eligibleUnitQuantityCode      = 'MTK' ;
$product->eligibleUnitQuantityWeight    = 6.4 ;

$product->eligiblePackageQuantityCode   = 'PK' ;
$product->eligiblePackageQuantityValue  = 0.456 ;
$product->eligiblePackageQuantityWeight = 2.9184 ;

$product->eligiblePalletQuantityCode    = 'PX' ;
$product->eligiblePalletQuantityValue   = 38.304 ;
$product->eligiblePalletQuantityWeight  = 245.1456 ;

$product->findEligibleQuantityByType( UnitOfSaleType::PARCEL )->weight ;  // 245.1456
```

🔑 **Nothing is computed.** A level that states no weight receives none — never a zero, which would read as "weightless" where the truth is "unknown". Deriving a missing measure from a volume and a density would produce a figure indistinguishable from a stated one; that belongs to whoever displays it, not to the class that carries it.

⚠️ **A level with no unit code assembles nothing**, weight or no weight: a measure with no level to name it has nowhere to go.

🔑 **The ratio between two levels restates the packaging chain** — how many pieces fit a package, how many packages fit a pallet — without any of those values being stored twice.

⚠️ **Distinct from `Product::$weight`**, inherited from the Schema.org mirror: that one is the weight of the billed unit, with no unit stated and no level attached. It does not change.

🔑 **The chain types itself all the way down.** `PhysicalQuantity::$valueReference` carries the hydration attribute, so `Reflection::hydrate()` walks to the last level: a weight reads `->weight` everywhere, never `['weight']` one step below. `findEligibleQuantityByType()` remains the direct way to a given level — **and the level it returns types the chain below it**, so the reading syntax does not change with the depth.

### Walking a tree you did not build

`findEligibleQuantityByType()` knows a single tree: the product's own. And **that tree is `null` on any product read back from a base** — it is built by the magic setter at import time, copied onto every offer, and it is that copy which is stored. The consumer holding a tree is therefore holding the offer's, not the product's.

The walk is then taken as a function, over any tree:

```php
use function xyz\oihana\schema\helpers\hydrate\findPhysicalQuantityByType;

$parcel = findPhysicalQuantityByType( UnitOfSaleType::PARCEL , $offer->eligibleQuantity ) ;

$parcel?->weight ;  // 245.1456
$parcel?->volume ;  // 2.6208
```

The tree handed in may be **typed or raw** — the rows a base read leaves — and the level returned is always a `PhysicalQuantity`, **the chain below it included**: `$parcel->valueReference->weight` reads like `$parcel->weight`, the syntax does not change with the depth.

🚨 **This is what makes losing the weight impossible rather than repairable.** Without that entry, whoever holds a tree writes the walk again by hand, and a walk written by hand rebuilds the levels as plain `QuantitativeValue`: that class declares neither weight nor volume, and a class discards the keys it does not declare. Both leave **without an error and without a trace**.

`Product::findEligibleQuantityByType()` is now that call on its own tree — same signature, same result.

⚠️ **The constructor assigns raw** — no attribute acts on that path. Use [`hydratePhysicalQuantity()`](helpers.md) there, which walks the chain explicitly; that is what `hydrateAggregateOffer()` does.

⚠️ Schema.org lets `valueReference` hold things that are not quantities — an enumeration, a qualitative value. **On `PhysicalQuantity` it is the next packaging level, and nothing else**: that is what the class exists for.

### The extension point: `resolveUnitCode()`

Unit codes sometimes arrive in a **proprietary nomenclature** (an ERP unit table). The `protected resolveUnitCode( mixed $value ) : ?string` hook returns the value unchanged by default; a subclass overrides it to translate its nomenclature into UN/CEFACT **before** the tree is built:

```php
class MyProduct extends Product
{
    protected function resolveUnitCode( mixed $value ) :?string
    {
        return is_scalar( $value ) ? MyUnitTable::toUNCEFACT( (string) $value ) : null ;
    }
}
```

---

## The other product properties

| Property | Type | Role |
|---|---|---|
| `unitOfSale` | `UnitOfSaleType` | Unit, package or pallet. |
| `inStock` / `inventoryLevel` | `bool` / `StockLevel` | Stock management and level. |
| `priceCategory` / `webCategory` / `productType` | term references | The pricing, navigation and functional classifications. |
| `vat` | `TaxRate` or reference | The VAT regime. |
| `density` / `length` / `volume` | numerics | The physical characteristics. |
| `fees` | `FeeSpecification[]` | The fees owed **on top of the price** — environmental contribution, deposit, packaging, carriage (see below). |
| `hasApplicableResource` | `ApplicableResource[]` | The resources that may be applied to it — a service, an option (see below). |
| `status` | `int` | The applicative status. |

The descriptive `ProductProperty` trait (essence, appearance, certification, colors, …) and the normalized additional properties (`ProductAdditionalProperty::normalize()`) complete the record — see [Ingestion](ingestion.md).

### The fees — `fees`

Some items owe an amount **on top of their price**: an environmental contribution, a deposit, packaging, carriage. Each one is a `FeeSpecification` — a `UnitPriceSpecification` augmented with the **published rate** it derives from.

```json
{
  "@type": "FeeSpecification",
  "priceComponentType": "https://schema.oihana.xyz/EnvironmentalFee",
  "price": 0.0157,
  "priceCurrency": "EUR",
  "unitCode": "C62",
  "identifier": "P4CHANT01",
  "publisher": { "@type": "Organization", "name": "Recycling Body" },
  "rate": { "@type": "UnitPriceSpecification", "price": 215, "priceCurrency": "EUR", "unitCode": "TNE" }
}
```

🔑 **`price` is expressed in the unit the item is billed in**, never in the unit of the rate. Applying a fee is therefore one multiplication — `amount = quantity of the line × price` — with no lookup table on the consumer side.

`rate` keeps the published rate beside it, in **its own** unit: here 215 EUR per tonne for an item billed by the piece. The two coexist on purpose — one computes, the other explains — exactly as an offer exposes a final `price` next to its `priceComponent[]`. A charged amount stays accountable without looking anywhere else.

**A list, not one property per kind of fee**: `PriceComponentType` already enumerates several, they all behave the same way, and one item may fall under more than one scheme.

🔑 **The rate is typed like the fee itself.** `FeeSpecification::$rate` carries the hydration attribute, so `Reflection::hydrate()` reads `->rate->price` where a raw array would read `['price']` — on a pair whose whole point is that both are read together.

⚠️ **The constructor assigns raw** — no attribute acts on that path. Use [`hydrateFeeSpecification()`](helpers.md) there: it types the `rate` and resolves the `publisher` from its `@type`, and leaves a rate already typed exactly as it stands.

#### When the fee cannot be priced

A rate stated per tonne needs a weight; a rate stated per piece needs to know how many pieces a package holds. When the catalogue does not say, **the fee is still owed** — it simply cannot be quantified.

The entry then exists **without a `price`**, carrying its `rate` and an `unresolvedReason` (→ `FeeUnresolvedReason`):

| Value | What it says |
| :--- | :--- |
| `MISSING_FEE_RATE` | no published rate is attached to the item |
| `MISSING_PRODUCT_MEASURE` | the measure the rate is stated in — weight, volume, thickness — is absent or zero |
| `UNKNOWN_PACKAGE_CONTENT` | the item is billed by the package, whose content is unknown |

🔑 **Read it together with the absence of `price`**: "this is owed, here is the published rate, and here is what stops us from quantifying it". A zero would say "nothing is owed", which is false. A consumer multiplying a quantity by an absent `price` gets zero or an error — **never a wrong amount**.

⚠️ **Not to be confused with `ExtraPriceSpecification`**, which also derives from `UnitPriceSpecification` but serves price segmentation and has nothing to do with fees.

### The applicable resources — `hasApplicableResource`

An item may **receive** something : a workshop service, a treatment, an option. Each possibility is an `ApplicableResource`, carrying three things — the resource, its rank, and **whether it applies by default**.

```json
{
  "@type": "Product",
  "id": "board-42",
  "hasApplicableResource":
  [
    { "@type": "ApplicableResource", "item": { "id": "srv-treatment" }, "position": 1, "appliedByDefault": true  },
    { "@type": "ApplicableResource", "item": { "id": "srv-polish"    }, "position": 2, "appliedByDefault": false }
  ]
}
```

🚨 **The flag belongs to the LINK, never to the resource.** The same service can apply by default to one item and be merely offered on another : a treatment included in the price of one board is an option on the next. Put on the resource itself, the flag would read « I apply everywhere » — which is false, and false in a way nothing shows, since the resource looks perfectly ordinary. **That is the whole reason this class exists** : a bare list of resources could not say it.

🔑 **`item` carries a reference, not a copy of the record.** The price, the unit and the availability of a resource live on its own record, and a price depends on who is buying : copying them here would freeze, at the moment the link is written, figures that belong to the moment it is read.

⚠️ **An absent `appliedByDefault` is not a `false`.** Absent says « the source does not tell » ; `false` says « the source says no ». A consumer that needs to tell one from the other can, and one that does not can read both as « not by default ».

🚨 **Why the class does not extend `ListItem`**, which names `item` and `position` already. That class types `item` as `?Thing`, and PHP forbids widening the type of an inherited property : a subclass may not turn it into `null|array|Thing`. Yet every class of this library is built from an array — `new Product( [ … ] )` — so an `item` unable to hold an array would throw on the constructor path while working through reflection, which is the worst of both. **The property names are identical**, so what the JSON-LD says is unchanged ; only the inheritance differs.

⚠️ **The constructor assigns raw**, as it does for fees. On that path, go through [`hydrateApplicableResource()`](helpers.md) : it types the `item` down to a `Product` and leaves an already typed one alone.

---

## Pricing conditions

A **`PricingCondition`** is a conditional pricing rule: a discount, a tariff substitution or an imposed net price granted to a scoped set of buyers on a scoped set of items, valid over a period. It is the sell-side twin of a provider buying condition; it is resolved most-specific-first for a given (customer, item, place) context.

What it carries reads in three moves:

| Part | Role |
|---|---|
| `selector` (`PricingConditionSelector`) | The scope: **who** (`customerScope` + `customerId`), **what** (`itemScope` + `itemId`, refined by `categoryLevel`) and **where** (`areaScope` + `areaServed`). |
| `adjustment` (list of `Adjustment`) | The first possible effect: a list of stacked discounts applied in order (each a signed percentage or amount — negative means a surcharge). Always a list, even for a single adjustment. |
| `substitutesSegment` (`PriceSegmentation`) | The second possible effect: the buyer's usual tariff segment is swapped — applied *instead of* a discount. |
| `fixedPrice` (`MonetaryAmount`) | The third possible effect: a fixed net price imposed *instead of* any adjustment or segment substitution. The three effects are mutually exclusive. |
| `free` (`bool`) | Whether the item is granted free of charge under this condition. |
| `excludedCustomers` / `excludedProducts` | The exceptions carved out of the scope. |
| `validFrom` / `validThrough` | The validity window. |
| `quantityDiscount` (`PriceQuantityDiscount`) | An optional quantity-tier effect. |

The scope resolves by decreasing granularity on three axes: on the buyer axis `INDIVIDUAL` outranks `GROUP`, which outranks `COMPANY`, which outranks `ALL`; on the item axis `PRODUCT` › `CATEGORY` › `PROVIDER` › `ALL`; on the place axis `WAREHOUSE` (one point of sale) outranks `COMPANY`, which outranks `GROUP`, which outranks `ALL` (everywhere). `areaScope` states the nature of the place carried by `areaServed`. The three axes rely on the `PricingTargetScope`, `PricingItemScope` and `PricingAreaScope` enumerations.

```json
{
  "@type": "PricingCondition",
  "selector": {
    "@type": "PricingConditionSelector",
    "customerScope": "https://schema.oihana.xyz/PricingTargetScope#Group",
    "customerId": "600214",
    "itemScope": "https://schema.oihana.xyz/PricingItemScope#Category",
    "itemId": "05",
    "categoryLevel": 1
  },
  "validThrough": "2026-12-31",
  "adjustment": [ { "@type": "Adjustment", "type": "https://schema.oihana.xyz/Discount", "percentage": 10 } ],
  "excludedCustomers": [ "600160" ]
}
```

## The customer-priced offer

A **`CustomerOffer`** is a sell offer aimed at **one specific customer**: the price of their tariff segment at their warehouse, optionally reached through a `PricingCondition`. It specializes `OfferForPurchase` and reuses the whole inherited pricing surface (`price`, `priceCurrency`, `priceSpecification`, `eligibleCustomerType` for the applied segment, `availableAtOrFrom` for the warehouse, `validFrom` / `validThrough`, `seller`), adding two own properties:

| Property | Role |
|---|---|
| `customer` | A lightweight reference to the beneficiary customer (`id`, `name`, `url`). |
| `appliedCondition` (`PricingCondition`) | The pricing condition that produced the price — `null` when the base tariff applies as-is. |

The `priceSpecification` is typically a `CompoundPriceSpecification` whose components decompose the list price (`ListPrice`), the optional discount (`Discount`) and the effective price (`SalePrice`); on the sell side, a `SellingMargin` component may carry the margin.

```json
{
  "@type": "CustomerOffer",
  "customer": { "@type": "Customer", "id": "216303", "name": "Menuiserie Fabre" },
  "eligibleCustomerType": { "@type": "BusinessEntityType", "id": 4, "name": "Pro." },
  "availableAtOrFrom": { "@type": "Warehouse", "id": "1", "name": "Main warehouse" },
  "price": 9.20,
  "priceCurrency": "EUR",
  "appliedCondition": {
    "@type": "PricingCondition",
    "adjustment": [ { "@type": "Adjustment", "type": "https://schema.oihana.xyz/Discount", "percentage": 7.15 } ]
  },
  "priceSpecification": {
    "@type": "CompoundPriceSpecification",
    "priceComponent": [
      { "@type": "UnitPriceSpecification", "price": 9.91, "priceType": "https://schema.org/ListPrice" },
      { "@type": "UnitPriceSpecification", "price": 7.15, "priceComponentType": "https://schema.oihana.xyz/Discount", "unitText": "%" },
      { "@type": "UnitPriceSpecification", "price": 9.20, "priceType": "https://schema.org/SalePrice" }
    ]
  }
}
```

## The satellite catalog

| Class | Role |
|---|---|
| `StockLevel` | The stock level, with its point of sale (`assignedPOS` hydrated as a `Warehouse`). |
| `TaxRate` | The VAT rate. |
| `PriceSegmentation` | The price segmentation of a customer or a product. |
| `ExtraPriceSpecification` | A surcharge/discount, convertible into a `UnitPriceSpecification` (`toUnitPriceSpecification()`). |
| `FeeSpecification` | A fee owed on top of the price, with the published rate it derives from (`rate`) and, when it cannot be priced, the reason (`unresolvedReason`). |
| `PhysicalQuantity` | A level of the eligible-quantity tree: a `QuantitativeValue` that also says what it weighs (`weight`) and what space it takes (`volume`). |
| `PriceQuantityDiscount` | The quantity discount. |
| `PricingCondition` / `PricingConditionSelector` | The pricing condition (discount or substitution) and its scope (see above). |
| `CustomerOffer` | The sell offer at one specific customer's tariff (segment × warehouse, optional condition); specializes `OfferForPurchase` (see above). |
| `PaymentCondition` / `PaymentMethod` | The accepted payment conditions and methods. |
| `ProductProviderInfo` | The buying information of a product at its supplier (price, margin, reference quantity). |
| `ProductWarehouseInfo` / `ProviderProductWarehouseInfo` | The per-warehouse product information, house side and supplier side. |
| `ProductWarehouseAvailability` | The availability of a product in a warehouse. |
| `ProductType` | The functional type of the product — `stockable`, `trackable`, plus the house display `color` (`#RRGGBB`, from [`HasColor`](thesaurus.md), the same hint the thesaurus families carry). |

## The enumerations

| Enumeration | Values | Usage |
|---|---|---|
| `UnitOfSaleType` | `UNIT` , `PACKAGE` , `PARCEL` | The levels of the quantity tree and the unit of sale (`…#Unit`, `…#Package`, `…#Parcel` URLs). |
| `FeeUnresolvedReason` | `MISSING_FEE_RATE` , `MISSING_PRODUCT_MEASURE` , `UNKNOWN_PACKAGE_CONTENT` | Why a fee that is owed could not be priced — carried by `FeeSpecification::$unresolvedReason`, read together with the absence of `price`. |
| `PriceType` | buying, selling, reference prices… | The type of a price in a specification. |
| `PriceComponentType` | the components of a price | The decomposition of a price (base, surcharges, fees) — also covers discount, surcharge, selling margin, environmental fee, deposit and packaging. |
| `BusinessEntityType` | professional, individual… | The customer segmentation of an offer. |
| `PricingTargetScope` | `INDIVIDUAL` , `GROUP` , `COMPANY` , `ALL` | The granularity of the buyer targeted by a `PricingCondition`. |
| `PricingItemScope` | `PRODUCT` , `CATEGORY` , `PROVIDER` , `ALL` | The granularity of the item targeted by a `PricingCondition`. |
| `PricingAreaScope` | `WAREHOUSE` , `COMPANY` , `GROUP` , `ALL` | The granularity of the place a `PricingCondition` applies at (the nature of the place carried by `areaServed`). |

---

## See also

- [Helper functions](helpers.md) — `hydrateStockLevel()`, `hydrateAggregateOffer()` and the other hydrators of this layer.
- [Oihana organizations](organizations.md) — `Provider` and its `ProductProviderInfo`.
- [Oihana places](places.md) — `Warehouse`, the depot referenced by stock and availability.
