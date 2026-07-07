# `xyz\oihana\schema\business\documents` — Business documents

The `xyz\oihana\schema\business\documents` namespace models the **quote → purchase order → invoice** cycle (and its neighbors: credit note, delivery note, receipt, statement). It reuses the Schema.org vocabulary wherever it exists (`customer`, `seller`, `MonetaryAmount`, `PriceSpecification`…) and draws on UBL 2.3 for the concepts Schema.org lacks (price adjustments, tax breakdown, payment schedule) — without copying the UBL XSDs.

> 🇫🇷 Cette page existe aussi en [français](../../fr/oihana/business-documents.md).

---

## Status of this namespace

This page documents the whole namespace: the **cross-cutting value objects** (`TaxDetail`, `Adjustment`…), the complete **document hierarchy** (`BusinessDocument`, `Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `DeliveryNote`, `Receipt`, `Statement`) and **export** (`BusinessDocumentExporter`, `JsonLdExporter`).

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
| Represent an invoice. | [`Invoice`](#invoice) |
| Represent a credit note correcting an invoice. | [`CreditNote`](#creditnote) |
| Represent a delivery note. | [`DeliveryNote`](#deliverynote) |
| Represent a payment receipt. | [`Receipt`](#receipt) |
| Represent a periodic account statement. | [`Statement`](#statement) / [`StatementEntry`](#statemententry) |
| Serialize a business document (JSON-LD, and tomorrow UBL/Factur-X…). | [`BusinessDocumentExporter`](#businessdocumentexporter) / [`JsonLdExporter`](#jsonldexporter) |

The value objects (`TaxDetail`, `Adjustment`…, as well as `StatementEntry`) extend `org\schema\StructuredValue` (like `MonetaryAmount` or `PriceSpecification`): they are structured values, not addressable resources. `BusinessDocument` and its flavors (`Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `DeliveryNote`, `Receipt`, `Statement`) extend `org\schema\Intangible` — see [`BusinessDocument`](#businessdocument) for the rationale behind that anchor. All of them share the `@context = 'https://schema.oihana.xyz'` distinguisher.

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

A full `Quote`, with its lines, a **discount applied to the whole document**, and its recap, hydrates the same way:

```php
use oihana\reflect\Reflection;
use xyz\oihana\schema\business\documents\Quote;
use xyz\oihana\schema\enumerations\BusinessDocumentStatus;
use xyz\oihana\schema\enumerations\PriceComponentType;

$quote = new Reflection()->hydrate
([
    Quote::CURRENCY       => 'EUR' ,
    Quote::ISSUE_DATE     => '2026-01-15' ,
    Quote::VALID_THROUGH  => '2026-02-15' ,
    Quote::STATUS         => BusinessDocumentStatus::DRAFT ,
    Quote::DOCUMENT_LINES => [ [ 'position' => 1 , 'quantity' => 5 ] ] ,
    Quote::ADJUSTMENTS    =>
    [
        [ 'type' => PriceComponentType::DISCOUNT , 'percentage' => 5 , 'reason' => 'Order-level discount' ] ,
    ],
    Quote::TOTALS =>
    [
        'total'          => [ 'value' => 114 , 'currency' => 'EUR' ] ,
        'allowanceTotal' => [ 'value' => 6   , 'currency' => 'EUR' ] ,
    ],
], Quote::class);

$quote->documentLines[ 0 ] instanceof \xyz\oihana\schema\business\documents\BusinessDocumentLine ; // true
$quote->adjustments[ 0 ]   instanceof \xyz\oihana\schema\business\documents\Adjustment ;            // true
$quote->totals             instanceof \xyz\oihana\schema\business\documents\DocumentTotals ;        // true
```

**Line vs document.** An `Adjustment` applies either to a single line (`BusinessDocumentLine::$adjustments` — a discount specific to one item) or to the whole document (`BusinessDocument::$adjustments` — a document-level discount, or a shipping/packaging fee charged globally), following UBL's `AllowanceCharge`. The combined effect of the document-level adjustments can be read back, if needed, from the optional derived fields `DocumentTotals::$allowanceTotal` (total allowances) and `DocumentTotals::$chargeTotal` (total charges/fees), mirroring UBL's `AllowanceTotalAmount`/`ChargeTotalAmount`.

**Chaining the cycle.** Each link references the upstream document through a `references*` property: `PurchaseOrder::$referencesQuote` (→ `Quote`), `Invoice::$referencesOrder` (→ `PurchaseOrder`), `CreditNote`/`Receipt::$referencesInvoice` (→ `Invoice`). These links are **collections**: each accepts a single document **or** several (a consolidated invoice, a payment settling several invoices, an order aggregating several quotes). Deep hydration is polymorphic — a single associative array yields one object, a list yields an array of objects:

```php
use oihana\reflect\Reflection;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\business\documents\PurchaseOrder;
use xyz\oihana\schema\business\documents\Quote;

// A purchase order originating from one accepted quote.
$order = new Reflection()->hydrate
([
    PurchaseOrder::CURRENCY         => 'EUR' ,
    PurchaseOrder::REFERENCES_QUOTE => [ Quote::CURRENCY => 'EUR' ] , // one quote → a Quote object
], PurchaseOrder::class);

$order->referencesQuote instanceof Quote ; // true

// A consolidated invoice billing two purchase orders.
$invoice = new Reflection()->hydrate
([
    Invoice::CURRENCY         => 'EUR' ,
    Invoice::REFERENCES_ORDER =>
    [
        [ PurchaseOrder::CURRENCY => 'EUR' ] ,
        [ PurchaseOrder::CURRENCY => 'EUR' ] ,
    ], // a list → an array of PurchaseOrder
], Invoice::class);

is_array( $invoice->referencesOrder ) && count( $invoice->referencesOrder ) === 2 ; // true
```

An `Invoice` references the `PurchaseOrder` it bills (not `org\schema\Order` — see [`Invoice`](#invoice) for why), then exports to JSON-LD via `JsonLdExporter`:

```php
use org\schema\enumerations\status\PaymentComplete;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\business\documents\export\JsonLdExporter;

$invoice = new Invoice
([
    Invoice::CURRENCY       => 'EUR' ,
    Invoice::ACCOUNT_ID     => 'ACC-001' ,
    Invoice::PAYMENT_STATUS => PaymentComplete::class ,
]);

echo new JsonLdExporter()->export( $invoice );
// {"@type":"Invoice","@context":"https://schema.oihana.xyz","accountId":"ACC-001","currency":"EUR","paymentStatus":"org\\schema\\enumerations\\status\\PaymentComplete"}
```

A `Statement` recaps the documents that moved an account's balance over a period, as a list of `StatementEntry`:

```php
use oihana\reflect\Reflection;
use xyz\oihana\schema\business\documents\Statement;
use xyz\oihana\schema\business\documents\StatementEntry;

$statement = new Reflection()->hydrate
([
    Statement::OPENING_BALANCE => [ 'value' => 0   , 'currency' => 'EUR' ] ,
    Statement::CLOSING_BALANCE => [ 'value' => 120 , 'currency' => 'EUR' ] ,
    Statement::ENTRIES =>
    [
        [ StatementEntry::DATE => '2026-01-15' , StatementEntry::DOCUMENT => 'INV-001' , StatementEntry::AMOUNT => [ 'value' => 120 , 'currency' => 'EUR' ] ] ,
    ],
], Statement::class);

$statement->entries[ 0 ] instanceof StatementEntry ; // true
```

---

## Class catalog

| Class | Extends | Role |
|---|---|---|
| <a id="taxdetail"></a>`TaxDetail` | `StructuredValue` | A tax (`category`, `rate`, `basisAmount`, `taxAmount`) applied on a line or a document. Never mixes VAT with environmental contributions — see `EcoFeeRule`/`AppliedEcoFee`. |
| <a id="adjustment"></a>`Adjustment` | `StructuredValue` | A price adjustment (`type`, `amount` or `percentage`, `reason`, `includedInBase`), inspired by UBL's `AllowanceCharge`. Covers discount, surcharge, shipping fee, environmental fee, deposit and packaging through the single `type` property (see `PriceComponentType`). |
| <a id="ecofeerule"></a>`EcoFeeRule` | `StructuredValue` | The calculation rule of an environmental fee (`category`, `rate`, `validFrom`, `validThrough`) — a catalog concept, with no monetary effect of its own. |
| <a id="appliedecofee"></a>`AppliedEcoFee` | `StructuredValue` | The record of an `EcoFeeRule` applied on a line (`rule`, `quantity`, `amount`) — the actual monetary effect always flows through an `Adjustment` of type `environmentalFee`. |
| <a id="documenttotals"></a>`DocumentTotals` | `StructuredValue` | The monetary summary of a document (`subtotal`, `totalTax`, `total`, `prepaidAmount`, `balanceDue`, plus the optional derived totals `allowanceTotal`/`chargeTotal` of the document-level adjustments, mirroring UBL `AllowanceTotalAmount`/`ChargeTotalAmount`), each amount a `MonetaryAmount`. A dedicated object rather than a reuse of `CompoundPriceSpecification`, whose Schema.org role (bundling prices that apply in parallel, e.g. electricity + cleaning) doesn't match a HT/tax/TTC recap. |
| <a id="businessdocumentline"></a>`BusinessDocumentLine` | `StructuredValue` | A document line (`item`, `position`, `quantity`, `unit`, `price`, `taxes`, `adjustments`, `subtotal`, `total`) — `taxes` and `adjustments` are scoped to the line, so a document can mix lines taxed at different rates. |
| <a id="paymentschedule"></a>`PaymentSchedule` | `StructuredValue` | A payment schedule (`installments`, a list of `PaymentInstallment`). Each installment carries its own payment status, so the plan can be tracked installment by installment; only reminders remain a later iteration. |
| <a id="paymentinstallment"></a>`PaymentInstallment` | `StructuredValue` | A single installment (`dueDate`, `amount` or `percentage`, `paymentStatus`). `paymentStatus` reuses `org\schema\enumerations\status\PaymentStatusType` (paid, due, past due…), the installment-level counterpart of the invoice's `paymentStatus`. |
| <a id="businessdocument"></a>`BusinessDocument` | `Intangible` | The common parent of the quote → order → invoice cycle: `adjustments` (document-level adjustments, see `Adjustment`), `attachments`, `currency`, `customer`, `documentLines`, `issueDate`, `paymentTerms`, `references`, `seller`, `status` (→ `BusinessDocumentStatus`), `taxes`, `totals`. Extends `Intangible` rather than reusing `org\schema\Order`/`org\schema\Invoice`: a business document qualifies a transaction, it is not an addressable resource in its own right — and this keeps the Schema.org mirror untouched (existing consumers of `org\schema\Order`/`Invoice` see no change). |
| <a id="quote"></a>`Quote` | `BusinessDocument` | A quote — adds `validThrough` (reusing the Schema.org property already carried by `PriceSpecification`/`Offer`, rather than a new name). Not to be confused with `org\schema\creativeWork\Quotation`, which is an unrelated **literary citation**. |
| <a id="purchaseorder"></a>`PurchaseOrder` | `BusinessDocument` | A purchase order — the customer's confirmed commitment, typically following the acceptance of a `Quote`: `referencesQuote` (→ one or more `Quote`), the upstream link of the cycle and the data behind the `BusinessDocumentStatus::CONVERTED` status. |
| <a id="invoice"></a>`Invoice` | `BusinessDocument` | An invoice — the final document of the quote → order → invoice cycle: `accountId`, `billingPeriod`, `broker`, `category`, `confirmationNumber`, `paymentDueDate`, `paymentStatus` (→ `org\schema\enumerations\status\PaymentStatusType`, reusing its existing member classes `PaymentComplete`/`PaymentDue`/`PaymentDeclined`/`PaymentPastDue`/`PaymentAutomaticallyApplied`), `provider`, `referencesOrder` (→ one or more of this namespace's own `PurchaseOrder`), `scheduledPaymentDate`. Reuses `org\schema\Invoice`'s property names, but deliberately does not share a property trait with it: `referencesOrder` must point at the house `PurchaseOrder` (not `org\schema\Order`), and some of the mirror's unions (`broker`, `category`, `billingPeriod`) predate the `null\|array\|X` convention — widening them for a shared trait would mean editing the mirror, which this hierarchy avoids (see [`BusinessDocument`](#businessdocument)). |
| <a id="creditnote"></a>`CreditNote` | `BusinessDocument` | A credit note — corrects or cancels all or part of an `Invoice` already issued: `reason` (free-text justification, same name/type as `Adjustment::$reason`), `referencesInvoice` (→ one or more `Invoice`). The corrected amount flows through the inherited `totals` (a positive recap); it's the document type (`CreditNote`) itself that carries the "this reduces what's owed" meaning, not a sign convention. |
| <a id="deliverynote"></a>`DeliveryNote` | `BusinessDocument` | A delivery note — attests the physical delivery of a `PurchaseOrder`'s goods: `orderDelivery` (→ `org\schema\ParcelDelivery`, reusing the property name and type already carried by `org\schema\Order` rather than re-inventing shipment tracking). |
| <a id="receipt"></a>`Receipt` | `BusinessDocument` | A receipt — proof that the payment of an `Invoice` was received: `confirmationNumber`, `paymentMethod`/`paymentMethodId` (reused from `org\schema\Invoice`), `referencesInvoice` (→ one or more `Invoice`). The received amount isn't duplicated here (already covered by the inherited `totals`); the date received is the inherited `issueDate`. |
| <a id="statement"></a>`Statement` | `BusinessDocument` | A statement — recaps, over a period, the documents that moved an account's balance: `billingPeriod` (reusing the name already used by `org\schema\Invoice`), `entries` (a list of `StatementEntry`), `openingBalance`/`closingBalance` (`MonetaryAmount`, no Schema.org equivalent — UBL names them `BeginningBalanceAmount`/`EndingBalanceAmount`). The only class of the lot that isn't a thin single-property subclass: it introduces its own line concept. |
| <a id="statemententry"></a>`StatementEntry` | `StructuredValue` | A `Statement` line: `document` (the related `BusinessDocument`, or a plain string when the full object isn't available), `date`, `amount`, `balance` (the running balance after this entry). Distinct from `BusinessDocumentLine`, which prices a product/service, not an account movement. |
| <a id="businessdocumentexporter"></a>`BusinessDocumentExporter` | *(interface)* | The serialization contract for a `BusinessDocument`: `export(BusinessDocument $document): string`. Regulatory formats (UBL, Factur-X, Peppol…) remain out of scope for now. |
| <a id="jsonldexporter"></a>`JsonLdExporter` | `BusinessDocumentExporter` | Demonstration implementation: delegates to `ThingTrait::jsonSerialize()` (inherited via `Intangible`/`Thing`) then `json_encode()`. |

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
