# `xyz\oihana\schema\business\documents` — Business documents

The `xyz\oihana\schema\business\documents` namespace models the **quote → purchase order → invoice** cycle (and its neighbors: credit note, delivery note, receipt, statement). It reuses the Schema.org vocabulary wherever it exists (`customer`, `seller`, `MonetaryAmount`, `PriceSpecification`…) and draws on UBL 2.3 for the concepts Schema.org lacks (price adjustments, tax breakdown, payment schedule) — without copying the UBL XSDs.

> 🇫🇷 Cette page existe aussi en [français](../../fr/oihana/business-documents.md).

---

## Status of this namespace

This page documents both the **cross-cutting value objects** (`TaxDetail`, `Adjustment`…) and the foundation of the **document hierarchy** (`BusinessDocument`, `Quote`, `PurchaseOrder`). Still to come in an upcoming release: `Invoice`, `CreditNote`, `DeliveryNote`, `Receipt`, `Statement` and the export interface ; this page will be completed accordingly.

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
| Carry the properties common to every business document (parties, dates, amounts, status…). | [`BusinessDocument`](#businessdocument) |
| Represent a quote. | [`Quote`](#quote) |
| Represent a purchase order. | [`PurchaseOrder`](#purchaseorder) |

The value objects (`TaxDetail`, `Adjustment`…) extend `org\schema\StructuredValue` (like `MonetaryAmount` or `PriceSpecification`): they are structured values, not addressable resources. `BusinessDocument` and its flavors (`Quote`, `PurchaseOrder`) extend `org\schema\Intangible` — see [`BusinessDocument`](#businessdocument) for the rationale behind that anchor. All of them share the `@context = 'https://schema.oihana.xyz'` distinguisher.

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

A full `Quote`, with its lines and payment schedule, hydrates the same way:

```php
use oihana\reflect\Reflection;
use xyz\oihana\schema\business\documents\Quote;
use xyz\oihana\schema\enumerations\BusinessDocumentStatus;

$quote = new Reflection()->hydrate
([
    Quote::CURRENCY       => 'EUR' ,
    Quote::ISSUE_DATE     => '2026-01-15' ,
    Quote::VALID_THROUGH  => '2026-02-15' ,
    Quote::STATUS         => BusinessDocumentStatus::DRAFT ,
    Quote::DOCUMENT_LINES => [ [ 'position' => 1 , 'quantity' => 5 ] ] ,
    Quote::TOTALS         => [ 'total' => [ 'value' => 120 , 'currency' => 'EUR' ] ] ,
], Quote::class);

$quote->documentLines[ 0 ] instanceof \xyz\oihana\schema\business\documents\BusinessDocumentLine ; // true
$quote->totals instanceof \xyz\oihana\schema\business\documents\DocumentTotals ;                    // true
```

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
| <a id="businessdocument"></a>`BusinessDocument` | `Intangible` | The common parent of the quote → order → invoice cycle: `attachments`, `currency`, `customer`, `documentLines`, `issueDate`, `paymentTerms`, `references`, `seller`, `status` (→ `BusinessDocumentStatus`), `taxes`, `totals`. Extends `Intangible` rather than reusing `org\schema\Order`/`org\schema\Invoice`: a business document qualifies a transaction, it is not an addressable resource in its own right — and this keeps the Schema.org mirror untouched (existing consumers of `org\schema\Order`/`Invoice` see no change). |
| <a id="quote"></a>`Quote` | `BusinessDocument` | A quote — adds `validThrough` (reusing the Schema.org property already carried by `PriceSpecification`/`Offer`, rather than a new name). Not to be confused with `org\schema\creativeWork\Quotation`, which is an unrelated **literary citation**. |
| <a id="purchaseorder"></a>`PurchaseOrder` | `BusinessDocument` | A purchase order — the customer's confirmed commitment, typically following the acceptance of a `Quote`. Carries no property of its own in this version. |

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
