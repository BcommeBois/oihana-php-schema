# `xyz\oihana\schema\business\documents` — Business documents

The `xyz\oihana\schema\business\documents` namespace models the **quote → purchase order → invoice** cycle (and its neighbors: credit note, delivery note, receipt, statement). It reuses the Schema.org vocabulary wherever it exists (`customer`, `seller`, `MonetaryAmount`, `PriceSpecification`…) and draws on UBL 2.3 for the concepts Schema.org lacks (price adjustments, tax breakdown, payment schedule) — without copying the UBL XSDs.

> 🇫🇷 Cette page existe aussi en [français](../../fr/oihana/business-documents.md).

---

## Status of this namespace

This page documents the **cross-cutting value objects** — the building blocks reused by every business document. The document hierarchy itself (`BusinessDocument`, `Quote`, `PurchaseOrder`, `Invoice`…) ships in an upcoming release ; this page will be completed accordingly.

---

## When to use them

| Need | Class |
|---|---|
| Break down a tax (VAT, contribution) on a line or a document. | [`TaxDetail`](#taxdetail) |
| Apply a discount, surcharge, shipping fee, environmental fee, deposit or packaging charge. | [`Adjustment`](#adjustment) |
| Define the calculation rule of an environmental fee. | [`EcoFeeRule`](#ecofeerule) |
| Trace the application of an environmental-fee rule on a line. | [`AppliedEcoFee`](#appliedecofee) |
| Summarize a document's amounts (excl. tax, tax, incl. tax, prepaid, due). | [`DocumentTotals`](#documenttotals) |
| Represent a line of a business document. | [`BusinessDocumentLine`](#businessdocumentline) |
| Spread a payment over several installments. | [`PaymentSchedule`](#paymentschedule) / [`PaymentInstallment`](#paymentinstallment) |

All these classes extend `org\schema\StructuredValue` (like `MonetaryAmount` or `PriceSpecification`): they are structured values, not addressable resources. They share the `@context = 'https://schema.oihana.xyz'` distinguisher.

---

## Quick example

```php
use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\BusinessDocumentLine;
use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\enumerations\PriceComponentType;

$line = new BusinessDocumentLine
([
    BusinessDocumentLine::POSITION => 1 ,
    BusinessDocumentLine::QUANTITY => 5 ,
    BusinessDocumentLine::TAXES       => [ [ 'category' => 'VAT' , 'rate' => 20.0 ] ] ,
    BusinessDocumentLine::ADJUSTMENTS =>
    [
        [ Adjustment::TYPE => PriceComponentType::DISCOUNT , Adjustment::PERCENTAGE => 10.0 ] ,
    ],
]);
```

As everywhere else in the library, the constructor only performs a raw assignment: `$line->taxes[0]` stays an array until you go through `new \oihana\reflect\Reflection()->hydrate(...)`, which honors each class's `#[HydrateWith]`/`#[HydrateAs]` attributes and turns the nested arrays into `TaxDetail`/`Adjustment`/`MonetaryAmount` objects.

---

## Class catalog

| Class | Extends | Role |
|---|---|---|
| <a id="taxdetail"></a>`TaxDetail` | `StructuredValue` | A tax (`category`, `rate`, `basisAmount`, `taxAmount`) applied on a line or a document. Never mixes VAT with environmental contributions — see `EcoFeeRule`/`AppliedEcoFee`. |
| <a id="adjustment"></a>`Adjustment` | `StructuredValue` | A price adjustment (`type`, `amount` or `percentage`, `reason`, `includedInBase`), inspired by UBL's `AllowanceCharge`. Covers discount, surcharge, shipping fee, environmental fee, deposit and packaging through the single `type` property (see `PriceComponentType`). |
| <a id="ecofeerule"></a>`EcoFeeRule` | `StructuredValue` | The calculation rule of an environmental fee (`category`, `rate`, `validFrom`, `validThrough`) — a catalog concept, with no monetary effect of its own. |
| <a id="appliedecofee"></a>`AppliedEcoFee` | `StructuredValue` | The record of an `EcoFeeRule` applied on a line (`rule`, `quantity`, `amount`) — the actual monetary effect always flows through an `Adjustment` of type `environmentalFee`. |
| <a id="documenttotals"></a>`DocumentTotals` | `StructuredValue` | The monetary summary of a document (`subtotal`, `totalTax`, `total`, `prepaidAmount`, `balanceDue`), each amount a `MonetaryAmount`. A dedicated object rather than a reuse of `CompoundPriceSpecification`, whose Schema.org role (bundling prices that apply in parallel, e.g. electricity + cleaning) doesn't match a HT/tax/TTC recap. |
| <a id="businessdocumentline"></a>`BusinessDocumentLine` | `StructuredValue` | A document line (`item`, `position`, `quantity`, `unit`, `price`, `taxes`, `adjustments`, `subtotal`, `total`) — `taxes` and `adjustments` are scoped to the line, so a document can mix lines taxed at different rates. |
| <a id="paymentschedule"></a>`PaymentSchedule` | `StructuredValue` | A payment schedule (`installments`, a list of `PaymentInstallment`). Base version: reminders and a per-installment status are a later iteration. |
| <a id="paymentinstallment"></a>`PaymentInstallment` | `StructuredValue` | A single installment (`dueDate`, `amount` or `percentage`). |

---

## Associated constants

Each class exposes its property constants through a dedicated trait under [`constants/traits/business/documents/`](../../src/xyz/oihana/schema/constants/traits/business/documents), aggregated in [`DocumentsTrait`](../../src/xyz/oihana/schema/constants/traits/business/DocumentsTrait.php), itself composed into [`BusinessTrait`](../../src/xyz/oihana/schema/constants/traits/BusinessTrait.php) and then into the global [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php) aggregator — unlike `BusinessIdentityTrait`/`UserProfileTrait`, no name collision was found, so these constants are directly reachable via `Oihana::RATE`, `Oihana::AMOUNT`, etc., in addition to the class constants (`TaxDetail::RATE`, `Adjustment::AMOUNT`…).

---

## Related reading

- [`xyz\oihana\schema\business`](business.md) — `BusinessIdentity`, `UserProfile`.
- [`xyz\oihana\schema\products`](products.md) — `PriceComponentType`, reused by `Adjustment::$type`.
- [`org\schema`](../schema-org/README.md) — `MonetaryAmount`, `PriceSpecification`, `StructuredValue`.
- [Getting started](../getting-started.md) — installation, hydration, JSON-LD basics.
- [API reference](../../../docs).
