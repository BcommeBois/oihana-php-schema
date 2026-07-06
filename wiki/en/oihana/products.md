# `xyz\oihana\schema\products` — The commerce layer

The `xyz\oihana\schema\products` namespace carries the library's **commerce layer**: `Product`, the product enriched with its selling metadata (unit of sale, eligible quantities, pricing categories, VAT, stock), and its constellation of satellite classes — stock levels, price specifications, payment conditions and methods, provider and warehouse information.

The heart of the model is **the eligible-quantity tree**: the unit → package → pallet chain describing how a product is packaged, from which every unit-of-sale conversion derives.

> 🇫🇷 Cette page est aussi disponible en [français](../../fr/oihana/products.md).

---

## When to use it

Reach for `Product` as soon as an article carries a commercial dimension: a unit of sale, a packaging, a reference price, a stock level. The class extends the Schema.org `SomeProducts` — a product document stays standard JSON-LD, enriched with the house context.

---

## The eligible-quantity tree

A product sells by the unit, the package or the pallet (`unitOfSale`, values of the `UnitOfSaleType` enumeration). The `eligibleQuantity` tree describes the full chain: each level is a `QuantitativeValue` (quantity, UN/CEFACT unit code, label) whose `valueReference` points to the upper level.

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
| `findEligibleQuantityByType( $type )` | `QuantitativeValue` or `null` | The tree level matching a `UnitOfSaleType`. |

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
| `status` | `int` | The applicative status. |

The descriptive `ProductProperty` trait (essence, appearance, certification, colors, …) and the normalized additional properties (`ProductAdditionalProperty::normalize()`) complete the record — see [Ingestion](ingestion.md).

---

## The satellite catalog

| Class | Role |
|---|---|
| `StockLevel` | The stock level, with its point of sale (`assignedPOS` hydrated as a `Warehouse`). |
| `TaxRate` | The VAT rate. |
| `PriceSegmentation` | The price segmentation of a customer or a product. |
| `ExtraPriceSpecification` | A surcharge/discount, convertible into a `UnitPriceSpecification` (`toUnitPriceSpecification()`). |
| `PriceQuantityDiscount` | The quantity discount. |
| `PaymentCondition` / `PaymentMethod` | The accepted payment conditions and methods. |
| `ProductProviderInfo` | The buying information of a product at its supplier (price, margin, reference quantity). |
| `ProductWarehouseInfo` / `ProviderProductWarehouseInfo` | The per-warehouse product information, house side and supplier side. |
| `ProductWarehouseAvailability` | The availability of a product in a warehouse. |
| `ProductType` | The functional type of the product (stock, tracking, rules…). |

## The enumerations

| Enumeration | Values | Usage |
|---|---|---|
| `UnitOfSaleType` | `UNIT` , `PACKAGE` , `PARCEL` | The levels of the quantity tree and the unit of sale (`…#Unit`, `…#Package`, `…#Parcel` URLs). |
| `PriceType` | buying, selling, reference prices… | The type of a price in a specification. |
| `PriceComponentType` | the components of a price | The decomposition of a price (base, surcharges, fees) — also covers discount, surcharge, environmental fee, deposit and packaging. |
| `BusinessEntityType` | professional, individual… | The customer segmentation of an offer. |

---

## See also

- [Helper functions](helpers.md) — `hydrateStockLevel()`, `hydrateAggregateOffer()` and the other hydrators of this layer.
- [Oihana organizations](organizations.md) — `Provider` and its `ProductProviderInfo`.
- [Oihana places](places.md) — `Warehouse`, the depot referenced by stock and availability.
