# `xyz\oihana\schema\business\documents` — Business documents

The `xyz\oihana\schema\business\documents` namespace models the **quote → purchase order → invoice** cycle (and its neighbors: credit note, delivery note, receipt, statement). It reuses the Schema.org vocabulary wherever it exists (`customer`, `seller`, `MonetaryAmount`, `PriceSpecification`…) and draws on UBL 2.3 for the concepts Schema.org lacks (price adjustments, tax breakdown, payment schedule) — without copying the UBL XSDs.

> 🇫🇷 Cette page existe aussi en [français](../../fr/oihana/business-documents.md).

---

## Status of this namespace

This page documents the whole namespace: the **cross-cutting value objects** (`TaxDetail`, `Adjustment`, `PaymentReminder`, `DeliveryLine`, `ProofOfDelivery`, `AgingSummary`, `GoodsReceiptLine`…), the complete **document hierarchy** (`BusinessDocument`, `Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `DebitNote`, `DeliveryNote`, `GoodsReceiptConfirmation`, `Receipt`, `RemittanceAdvice`, `Statement`) and **export** (`BusinessDocumentExporter`, `JsonLdExporter`).

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
| Record payment reminders (dunning notices sent to the customer). | [`PaymentReminder`](#paymentreminder) |
| Carry the properties common to every business document (parties, dates, amounts, status…). | [`BusinessDocument`](#businessdocument) |
| Represent a quote. | [`Quote`](#quote) |
| Represent a purchase order. | [`PurchaseOrder`](#purchaseorder) |
| Represent an invoice. | [`Invoice`](#invoice) |
| Represent a credit note correcting an invoice. | [`CreditNote`](#creditnote) |
| Represent a debit note (the inverse of a credit note, increases what's owed). | [`DebitNote`](#debitnote) |
| Represent a delivery note. | [`DeliveryNote`](#deliverynote) |
| Detail, line by line, what was actually delivered (versus what was ordered). | [`DeliveryLine`](#deliveryline) |
| Record the confirmation that a delivery was received (signatory, date, noted discrepancy). | [`ProofOfDelivery`](#proofofdelivery) |
| Confirm, buyer-side, the receipt of goods (received quantities, discrepancies). | [`GoodsReceiptConfirmation`](#goodsreceiptconfirmation) / [`GoodsReceiptLine`](#goodsreceiptline) |
| Represent a payment receipt (seller-side). | [`Receipt`](#receipt) |
| Represent a remittance advice (payer-side). | [`RemittanceAdvice`](#remittanceadvice) |
| Represent a periodic account statement. | [`Statement`](#statement) / [`StatementEntry`](#statemententry) |
| Break down a customer balance by days overdue (aging). | [`AgingSummary`](#agingsummary) |
| Serialize a business document (JSON-LD, and tomorrow UBL/Factur-X…). | [`BusinessDocumentExporter`](#businessdocumentexporter) / [`JsonLdExporter`](#jsonldexporter) |

The value objects (`TaxDetail`, `Adjustment`…, as well as `StatementEntry`, `GoodsReceiptLine`) extend `org\schema\StructuredValue` (like `MonetaryAmount` or `PriceSpecification`): they are structured values, not addressable resources. `BusinessDocument` and its flavors (`Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `DebitNote`, `DeliveryNote`, `GoodsReceiptConfirmation`, `Receipt`, `RemittanceAdvice`, `Statement`) extend `org\schema\Intangible` — see [`BusinessDocument`](#businessdocument) for the rationale behind that anchor. All of them share the `@context = 'https://schema.oihana.xyz'` distinguisher.

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

The same need comes up in isolation when only the `documentLines` array is available — for instance a server response where the `BusinessDocument` was already built elsewhere and never went through `Reflection::hydrate()`. The [`hydrateDocumentLine()`](helpers.md#xyzoihanaschemahelpershydratedocuments--the-document-hydrators) helper covers exactly that case, raw or already-hydrated lines, a single line or a list.

Three other header slots are in the same situation, and each has its helper: [`hydrateDocumentTotals()`](helpers.md#xyzoihanaschemahelpershydratedocuments--the-document-hydrators) for the recap, [`hydrateAdjustment()`](helpers.md#xyzoihanaschemahelpershydratedocuments--the-document-hydrators) for the document-level adjustments, and [`hydrateParcelDelivery()`](helpers.md#xyzoihanaschemahelpershydrate--the-business-hydrators) for the delivery. The first two go through reflection, which **recurses**: a single call on an adjustment types its `amount`, its `taxes`, and the `basisAmount`/`taxAmount` each `TaxDetail` declares in its turn.

🔑 The lines and the adjustments are the only two slots where an **empty list is returned as it came** rather than reduced to `null`: a draft is commonly born without a single line, and "there are none" is an answer worth serving.

**The party unions.** `customer`, `seller` and `author` (and, on `Invoice`, `broker`/`provider`) are typed `Organization|Person` — a union a property type alone cannot settle, reflection otherwise keeping the first declared member whatever the payload says. Each therefore carries a `#[HydrateWith(Organization::class, Person::class)]`: `Reflection::hydrate()` then picks the class from the payload's discriminator (`@type`, `atType` or `type`) and, failing that, from the properties present. Same mechanism for the `item` of a `BusinessDocumentLine`, a `DeliveryLine` or a `GoodsReceiptLine` (`Product|Service`). A raw identifier (string or integer) passed instead of an object stays unchanged.

The [`hydrateBusinessDocument()`](helpers.md#xyzoihanaschemahelpershydratedocuments--the-document-hydrators) helper is therefore not required for that resolution: it adds the input shapes (a single document, a list of documents, passthrough) and serves any link in the cycle through its second parameter (`hydrateBusinessDocument( $raw , Quote::class )`, `Invoice::class`...).

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

**Line vs document.** An `Adjustment` applies either to a single line (`BusinessDocumentLine::$adjustments` — a discount specific to one item) or to the whole document (`BusinessDocument::$adjustments` — a document-level discount, or a shipping/packaging fee charged globally), following UBL's `AllowanceCharge`. The combined effect of the document-level adjustments can be read back, if needed, from the optional derived fields `DocumentTotals::$allowanceTotal` (total allowances) and `DocumentTotals::$chargeTotal` (total charges/fees), mirroring UBL's `AllowanceTotalAmount`/`ChargeTotalAmount`. Those derived fields state a total, never its origin: the tax an adjustment owes is read on the adjustment itself (`Adjustment::$taxes`), in the same shape as the line taxes.

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

A `Statement` recaps the documents that moved an account's balance over a period, as a list of `StatementEntry`. Each entry can carry its **type** (invoice, payment, credit note…) and its **due date**, and the statement can expose an **aging breakdown** (`AgingSummary`) — the balance split by days overdue, which the consumer computes and fills in:

```php
use oihana\reflect\Reflection;
use xyz\oihana\schema\business\documents\AgingSummary;
use xyz\oihana\schema\business\documents\Statement;
use xyz\oihana\schema\business\documents\StatementEntry;
use xyz\oihana\schema\enumerations\StatementEntryType;

$statement = new Reflection()->hydrate
([
    Statement::OPENING_BALANCE => [ 'value' => 0   , 'currency' => 'EUR' ] ,
    Statement::CLOSING_BALANCE => [ 'value' => 120 , 'currency' => 'EUR' ] ,
    Statement::TOTAL_DEBIT     => [ 'value' => 120 , 'currency' => 'EUR' ] ,
    Statement::ENTRIES =>
    [
        [
            StatementEntry::TYPE     => StatementEntryType::INVOICE ,
            StatementEntry::DATE     => '2026-01-15' ,
            StatementEntry::DUE_DATE => '2026-02-15' ,
            StatementEntry::DOCUMENT => 'INV-001' ,
            StatementEntry::AMOUNT   => [ 'value' => 120 , 'currency' => 'EUR' ] ,
        ],
    ],
    Statement::AGING_SUMMARY =>
    [
        AgingSummary::CURRENT => [ 'value' => 100 , 'currency' => 'EUR' ] ,
        AgingSummary::OVER_90 => [ 'value' => 20  , 'currency' => 'EUR' ] ,
    ],
], Statement::class);

$statement->entries[ 0 ] instanceof StatementEntry ;       // true
$statement->agingSummary instanceof AgingSummary ;         // true
```

The signed `amount` stays the sufficient movement value; `debitAmount`/`creditAmount` are an **optional** debit/credit split (UBL's / double-entry accounting's separate columns), complementing `amount`, not replacing it.

An overdue installment can carry its **reminders** — the trace of the dunning notices sent, with any late-payment charges expressed as `Adjustment` (never a bespoke "penalty" field):

```php
use oihana\reflect\Reflection;
use org\schema\enumerations\status\CompletedActionStatus;
use xyz\oihana\schema\business\documents\PaymentInstallment;
use xyz\oihana\schema\business\documents\PaymentReminder;
use xyz\oihana\schema\enumerations\PaymentReminderChannel;
use xyz\oihana\schema\enumerations\PaymentReminderLevel;

$installment = new Reflection()->hydrate
([
    PaymentInstallment::DUE_DATE  => '2026-02-01' ,
    PaymentInstallment::REMINDERS =>
    [
        [
            PaymentReminder::DATE           => '2026-02-20' ,
            PaymentReminder::LEVEL          => PaymentReminderLevel::SECOND_REMINDER ,
            PaymentReminder::CHANNEL        => PaymentReminderChannel::EMAIL ,
            PaymentReminder::STATUS         => CompletedActionStatus::class ,
            PaymentReminder::AMOUNT_CLAIMED => [ 'value' => 120 , 'currency' => 'EUR' ] ,
            PaymentReminder::ADJUSTMENTS    =>
            [
                [ 'type' => 'surcharge' , 'reason' => 'Late fee' , 'amount' => [ 'value' => 40 , 'currency' => 'EUR' ] ] ,
            ],
        ],
    ],
], PaymentInstallment::class);

$installment->reminders[ 0 ] instanceof PaymentReminder ;                 // true
$installment->reminders[ 0 ]->adjustments[ 0 ]->amount->value === 40 ;   // true
```

Reminders also exist at the level of the whole schedule (`PaymentSchedule::$reminders`), not only per installment.

A `DeliveryNote` can detail, line by line, what was actually delivered versus what was ordered (partial delivery, backorder), and carry the delivery confirmation:

```php
use oihana\reflect\Reflection;
use xyz\oihana\schema\business\documents\DeliveryLine;
use xyz\oihana\schema\business\documents\DeliveryNote;
use xyz\oihana\schema\business\documents\ProofOfDelivery;

$note = new Reflection()->hydrate
([
    DeliveryNote::LINES =>
    [
        [
            DeliveryLine::POSITION           => 1 ,
            DeliveryLine::ORDERED_QUANTITY   => 100 ,
            DeliveryLine::DELIVERED_QUANTITY => 80 ,
            DeliveryLine::BACKORDER_QUANTITY => 20 ,
            DeliveryLine::BACKORDER_REASON   => 'Out of stock' ,
        ],
    ],
    DeliveryNote::PROOF_OF_DELIVERY =>
    [
        ProofOfDelivery::SIGNATORY => 'Jane Doe' ,
        ProofOfDelivery::DATE      => '2026-01-20' ,
    ],
], DeliveryNote::class);

$note->lines[ 0 ] instanceof DeliveryLine ;             // true
$note->lines[ 0 ]->backorderQuantity === 20 ;           // true
$note->proofOfDelivery instanceof ProofOfDelivery ;     // true
```


### A harvested delivery note, in full

This example shows in context what no description conveys as quickly: **two lines carrying the same `position`, two orders, two invoices.**

```json
{
  "@context": "https://schema.oihana.xyz",
  "@type": "DeliveryNote",
  "identifier": "BL-1150243",
  "authority": "https://schema.oihana.xyz/BusinessDocumentAuthority#Mirrored",
  "issueDate": "2026-08-04",

  "referencesOrder": [
    { "@type": "PurchaseOrder", "identifier": "CDE-1148902" },
    { "@type": "PurchaseOrder", "identifier": "CDE-1149355" }
  ],

  "orderDelivery": {
    "@type": "ParcelDelivery",
    "deliveryAddress": { "@type": "PostalAddress", "streetAddress": "12 Industrial Way", "postalCode": "64240", "addressLocality": "Riverton" },
    "hasDeliveryMethod": { "@type": "DeliveryMethodTerm", "id": "LIV", "name": "Delivery" },
    "hasDeliveryRoute":  { "@type": "DeliveryRouteTerm", "id": "53", "name": "Route 53" },
    "requestedDeliveryDate": "2026-08-05"
  },

  "weight": { "value": 1731.375, "unitCode": "KGM", "unitText": "Kilogram" },
  "volume": { "value": 3.412,    "unitCode": "MTQ", "unitText": "Cubic Meter" },

  "lines": [
    {
      "@type": "DeliveryLine",
      "position": 1,
      "referencesOrder":   { "@type": "PurchaseOrder", "identifier": "CDE-1148902" },
      "referencesInvoice": { "@type": "Invoice", "identifier": "INV-2026-04417" },
      "item": { "@type": "Product", "id": "111190", "name": "Wood fibre board 40mm" },
      "orderedQuantity":   { "value": 120, "unitCode": "MTK", "unitText": "Square Meter" },
      "deliveredQuantity": { "value": 84,  "unitCode": "MTK", "unitText": "Square Meter" },
      "backorderQuantity": { "value": 36,  "unitCode": "MTK", "unitText": "Square Meter" },
      "backorderReason": "Supplier shortage",
      "weight": { "value": 537.6, "unitCode": "KGM", "unitText": "Kilogram" }
    },
    {
      "@type": "DeliveryLine",
      "position": 1,
      "referencesOrder":   { "@type": "PurchaseOrder", "identifier": "CDE-1149355" },
      "referencesInvoice": { "@type": "Invoice", "identifier": "INV-2026-04418" },
      "item": { "@type": "Product", "id": "111192", "name": "Batten 60x40" },
      "deliveredQuantity": { "value": 1467, "unitCode": "MTR", "unitText": "Metre" },
      "weight": { "value": 1193.775, "unitCode": "KGM", "unitText": "Kilogram" }
    }
  ]
}
```

Three readings the document makes obvious:

- **a `position` alone names nothing.** Both lines carry the same one, and they belong to two different orders — hence `referencesOrder` on each, and `referencesInvoice` following the same logic: the note is billed by **two** invoices, one per order, which a reference on the header could not express;
- **the sum checks out by eye**: `537.6 + 1193.775 = 1731.375`, the note's `weight`;
- **a line weighs what left**: the first delivered 84 of the 120 square meters ordered, and its weight is that of the 84.

---

## Class catalog

| Class | Extends | Role |
|---|---|---|
| <a id="taxdetail"></a>`TaxDetail` | `StructuredValue` | A tax (`category`, `rate`, `basisAmount`, `taxAmount`) applied on a line or a document. Never mixes VAT with environmental contributions — see `EcoFeeRule`/`AppliedEcoFee`. |
| <a id="adjustment"></a>`Adjustment` | `StructuredValue` | A price adjustment (`type`, `amount` or `percentage`, `reason`, `includedInBase`, `includedInTotal`, `taxes`), inspired by UBL's `AllowanceCharge`. Covers discount, surcharge, shipping fee, environmental fee, deposit and packaging through the single `type` property (see `PriceComponentType`). `includedInTotal` says whether the adjustment **counts towards the document totals** — **its absence means it counts**, only one left out says so, with `false`. ⚠️ Not to be confused with `includedInBase`, which answers something else entirely: that one says the amount is **already inside** the base price rather than added on top of it; this one says whether it reaches the totals at all. An adjustment can perfectly well be added to the price **and** left out of the total — a source may show a charge on a document and refuse to commit to it while an option is still open, and summing it anyway would overstate what is owed. 🔑 **The same name, the same default and the same meaning as `BusinessDocumentLine::$includedInTotal`**: a flag calling itself something else on the grounds that it lives on another class would be a missed opportunity, a consumer learning the rule once. An adjustment with a monetary effect may carry the tax it owes on its own account, at a rate of its own: the VAT on a shipping fee answers to the carrier, not to the goods delivered. |
| <a id="ecofeerule"></a>`EcoFeeRule` | `StructuredValue` | The calculation rule of an environmental fee (`category`, `rate`, `validFrom`, `validThrough`) — a catalog concept, with no monetary effect of its own. `rate` is a `UnitPriceSpecification` and **not** a `MonetaryAmount`: a contribution is charged on a physical measure — a weight, a surface, a volume, a count — never on a price, so the unit is not decoration, it is half the rule. A `MonetaryAmount` could only say "215 EUR" where the rule says "215 EUR **per tonne**". `category` carries what the rule applies to: the name says category because that is the common case, but the union accepts any `Thing` — a product included — and a rule attached to a single item is as legitimate as one attached to a family; a source publishing its rates item by item is not an exception to model around. A bare string names it by reference, without joining the record. |
| <a id="appliedecofee"></a>`AppliedEcoFee` | `StructuredValue` | The record of an `EcoFeeRule` applied on a line (`rule`, `quantity`, `amount`) — the actual monetary effect always flows through an `Adjustment` of type `environmentalFee`. |
| <a id="documenttotals"></a>`DocumentTotals` | `StructuredValue` | The monetary summary of a document (`subtotal`, `totalTax`, `total`, `prepaidAmount`, `balanceDue`, plus the optional derived totals `allowanceTotal`/`chargeTotal` of the document-level adjustments, mirroring UBL `AllowanceTotalAmount`/`ChargeTotalAmount`), each amount a `MonetaryAmount`. A dedicated object rather than a reuse of `CompoundPriceSpecification`, whose Schema.org role (bundling prices that apply in parallel, e.g. electricity + cleaning) doesn't match a HT/tax/TTC recap. |
| <a id="businessdocumentline"></a>`BusinessDocumentLine` | `StructuredValue` | A document line (`item`, `position`, `quantity`, `unit`, `price`, `taxes`, `adjustments`, `subtotal`, `total`, `additionalProperty`, `color`, `freeReason`, `includedInTotal`, `section`, `technicalNote`) — `taxes` and `adjustments` are scoped to the line, so a document can mix lines taxed at different rates. `additionalProperty` (`PropertyValue` pairs, hydrated by `Reflection::hydrate()`) carries the characteristics no dedicated property covers — a lot number, a serial number, an ERP-specific line flag. `color` (composed from `HasColor`, the same `#RRGGBB` house hint the thesaurus families and `ProductType` carry) lets the line be tinted for its own reason, without walking back to the item and its type. `freeReason` (→ `DefinedTerm`) says why the goods leave without being invoiced — a gift, a breakage, a sample, goods that were the customer's to begin with: **its presence is what says the line is offered**, no boolean sits beside it, a flag and a reason kept in parallel always ending up drifting apart. The term is frozen by its code and its label (`id` + `name`), never by its storage key, which a re-harvested vocabulary renumbers. `technicalNote` is the sibling of the inherited `description`: the latter is what the customer reads, the former is meant for whoever prepares the goods (« reprendre les 3 colis palette 12, ne pas remettre en stock ») — keeping the two apart is what lets a single document be printed twice, for two audiences. It serializes like any other property: leaving it out is the customer-facing renderer's job, the type does not do it on its own. `includedInTotal` says whether the line counts towards the document totals — **its absence means it counts**, only a line left out says so, with `false`. A priced line is not always a line to pay for: a quote may offer the same work twice — two floorings, two finishes — and expect the customer to keep one; both are printed, both are costed, one is billed. The lines of the discarded option are ordinary lines, with an item, a quantity and a price: nothing about their content sets them apart, and summing them anyway has been measured to overstate a real quote by a factor of two. The same slot serves anything shown but not owed — an informational fee, a figure quoted for reference; not to be read as "this line is a variant", the reason a line is left out is not its business, only the fact is. `section` carries the heading the line belongs to — "roof frame", "oak flooring", "laying" — a plain label repeated on every line of the same group, and deliberately nothing more: no section class, no nesting, no subtotal of its own. The grouping is whatever shares the label, which is enough to print a document in chapters and cheap enough to be worth carrying even when nothing reads it yet. 🚨 **Never store the group's subtotal here or beside it**: a recap amount living next to the lines it recaps is the shortest path to counting the same goods twice — it is derived, and it stays derived. Distinct from the inherited `description`, which names the item itself: a description belongs to one line, a heading is shared by several. |
| <a id="paymentschedule"></a>`PaymentSchedule` | `StructuredValue` | A payment schedule (`installments`, a list of `PaymentInstallment`; `reminders`, the plan-level reminders). Each installment carries its own payment status and its own reminders, so the plan can be tracked installment by installment. |
| <a id="paymentinstallment"></a>`PaymentInstallment` | `StructuredValue` | A single installment (`dueDate`, `amount` or `percentage`, `paymentStatus`, `reminders`). `paymentStatus` reuses `org\schema\enumerations\status\PaymentStatusType` (paid, due, past due…), the installment-level counterpart of the invoice's `paymentStatus`; `reminders` lists the `PaymentReminder` specific to this installment. |
| <a id="paymentreminder"></a>`PaymentReminder` | `StructuredValue` | The record of a payment reminder (`date`, `level` → `PaymentReminderLevel`, `channel` → `PaymentReminderChannel`, `status` → `org\schema\enumerations\status\ActionStatusType`, `amountClaimed`, `adjustments`, `note`). A trace, not an engine: the sending/scheduling logic stays consumer-side. Late-payment charges go through an `Adjustment` (never a bespoke "penalty" field). Attachable to an installment or to the whole schedule. |
| <a id="businessdocument"></a>`BusinessDocument` | `Intangible` | The common parent of the quote → order → invoice cycle: `adjustments` (document-level adjustments, see `Adjustment`), `assignedSeller`, `attachments`, `author`, `authority` (→ `BusinessDocumentAuthority`), `billingAddress`, `contact`, `currency`, `customer`, `direction` (→ `BusinessDocumentDirection`), `documentLines`, `issueDate`, `orderDelivery`, `paymentTerms`, `pointOfSale`, `references`, `seller`, `status` (→ `BusinessDocumentStatus`), `taxes`, `totals`, `volume`, `weight`. The four header properties `billingAddress` (`PostalAddress` — the billing address), `contact` (`Person` — the interlocutor, absent from the Schema.org vocabulary), `orderDelivery` (`ParcelDelivery` — the delivery address, method, route and requested date) and `pointOfSale` (`Place` — the outlet the document is bound to) complete the parties (`customer`/`seller`) and are stored as frozen copies, so the document stays self-contained even if the underlying records change later. Also added: `direction` (→ `BusinessDocumentDirection`: the document's commercial direction — sale or purchase — from the operator's point of view, telling which of the `seller`/`customer` parties is its own organization; orthogonal to the document type and to its `BusinessDocumentStatus` lifecycle) and `author` (`Organization`/`Person` — the party who authored the document, reusing the Schema.org `author` name). `assignedSeller` (`Person`, or its raw reference) names the **salesperson** the document is assigned to, not to be confused with `seller`, the party who **issues** it: one organization issues every document of a cycle, so only `assignedSeller` can answer "whose quotes are these?" — the question `sellerKey()` scopes a salesperson's own resources by, exactly as `Customer::$assignedSeller` does on the customer side. A document commonly gets its value from the customer record and then keeps it: reassigning a customer next year must not rewrite who took this year's order. No `#[HydrateAs]` is declared and none is needed: the union names a single class, so `Reflection::hydrate()` resolves a joined row into a `Person` on its own — and each entry of a joined list — while a bare key (string or integer) is left as read. `authority` (→ `BusinessDocumentAuthority`) says **which system holds the truth** about the document: ours, or the one it was harvested from. **Its absence means the document is ours** — only what comes from elsewhere states it, so nothing written before the property existed changes meaning. It answers a question no other property does: the class says what the document *is*, `direction` says which side of the trade we stand on, `status` says where it stands in its lifecycle — none of them says **who may change it**. The distinction only becomes visible when documents of both origins share a collection: a quote drafted here and an invoice mirrored from an ERP look alike, yet editing the second is a correction the next refresh erases without a word, and whoever made it never learns it was lost. ⚠️ Carrying the fact is not enforcing it: refusing the write belongs to whoever exposes the document — a schema carries meaning, not permissions. `weight` says **what the goods the document covers weigh** — the figure printed on a delivery note or a quote, and the one a carrier is quoted from. It is usually the sum, over the lines, of the quantity by the weight of the unit it is expressed in. A plain number carries it when the unit is implicit, a `QuantitativeValue` when the unit is stated (`{ value: 326.5456, unitCode: "KGM" }`); an array is hydrated as the latter. The union is the one of `OfferShippingDetails::$weight` widened with `array`, so a weight reads the same wherever it is met: the widening is what lets a raw row sit in the property until hydration replaces it, `#[HydrateAs]` acting through `Reflection::hydrate()` and never through the constructor. The mirror's union predates that convention, and the mirror is left untouched. ⚠️ **Not to be filed under `totals`** despite the pull of the name: that class is the **monetary** summary and every one of its properties is a `MonetaryAmount` — a mass sitting among them would blur what it is. `volume` is its twin — the space the same goods take up, read exactly the same way (`{ "value": 3.412, "unitCode": "MTQ" }`), and kept out of `totals` for the same reason. The property is deliberately neutral about gross and net: should the distinction ever be needed, it belongs to the `additionalType` of a `QuantitativeValue`, never to a second property — two weights held in parallel eventually disagree. Extends `Intangible` rather than reusing `org\schema\Order`/`org\schema\Invoice`: a business document qualifies a transaction, it is not an addressable resource in its own right — and this keeps the Schema.org mirror untouched (existing consumers of `org\schema\Order`/`Invoice` see no change). |
| <a id="quote"></a>`Quote` | `BusinessDocument` | A quote — adds `validThrough` (reusing the Schema.org property already carried by `PriceSpecification`/`Offer`, rather than a new name). Not to be confused with `org\schema\creativeWork\Quotation`, which is an unrelated **literary citation**. |
| <a id="purchaseorder"></a>`PurchaseOrder` | `BusinessDocument` | A purchase order — the customer's confirmed commitment, typically following the acceptance of a `Quote`: `referencesQuote` (→ one or more `Quote`), the upstream link of the cycle and the data behind the `BusinessDocumentStatus::CONVERTED` status. |
| <a id="invoice"></a>`Invoice` | `BusinessDocument` | An invoice — the final document of the quote → order → invoice cycle: `accountId`, `billingPeriod`, `broker`, `category`, `confirmationNumber`, `paymentDueDate`, `paymentStatus` (→ `org\schema\enumerations\status\PaymentStatusType`, reusing its existing member classes `PaymentComplete`/`PaymentDue`/`PaymentDeclined`/`PaymentPastDue`/`PaymentAutomaticallyApplied`), `provider`, `referencesDeliveryNote` (→ one or more `DeliveryNote`), `referencesOrder` (→ one or more of this namespace's own `PurchaseOrder`), `scheduledPaymentDate`. Reuses `org\schema\Invoice`'s property names, but deliberately does not share a property trait with it: `referencesOrder` must point at the house `PurchaseOrder` (not `org\schema\Order`), and some of the mirror's unions (`broker`, `category`, `billingPeriod`) predate the `null\|array\|X` convention — widening them for a shared trait would mean editing the mirror, which this hierarchy avoids (see [`BusinessDocument`](#businessdocument)). 🔑 `referencesDeliveryNote` is often **the only way to the invoice's lines**: an end-of-month invoice is built from what left the warehouse during the period, and a source that stamps the invoice number on the delivery — not on each line — leaves this link as the sole path back to what was actually billed. It is the sibling of `referencesOrder`, not a substitute for it: orders say what was committed to, deliveries say what went out. An order can be invoiced across two months, and one invoice can settle deliveries belonging to several orders — neither reference alone describes that. |
| <a id="creditnote"></a>`CreditNote` | `BusinessDocument` | A credit note — corrects or cancels all or part of an `Invoice` already issued: `reason` (free-text justification, same name/type as `Adjustment::$reason`), `reasonCode` (→ `CreditNoteReasonCode`, the structured cause alongside the free text), `referencesInvoice` (→ one or more `Invoice`), `remainingBalance` (the not-yet-applied part of the credit, cf. Xero `RemainingCredit` / QuickBooks `Balance`), `disposition` (→ `CreditNoteDisposition`: refunded, reapplied or pending). The corrected amount flows through the inherited `totals` (a positive recap); it's the document type (`CreditNote`) itself that carries the "this reduces what's owed" meaning, not a sign convention. |
| <a id="debitnote"></a>`DebitNote` | `BusinessDocument` | A debit note — the symmetric inverse of a credit note: it **increases** what the customer owes, correcting an under-billed invoice: `reason`, `referencesInvoice` (→ one or more `Invoice`). UBL defines it as its own document type; the adjusting amount flows through the inherited `totals`, the "this increases what's owed" meaning carried by the document type itself. |
| <a id="deliverynote"></a>`DeliveryNote` | `BusinessDocument` | A delivery note — attests the physical delivery of a `PurchaseOrder`'s goods: `orderDelivery` (→ `org\schema\ParcelDelivery`, reusing the property name and type already carried by `org\schema\Order` rather than re-inventing shipment tracking — including `hasDeliveryRoute`, the circuit the goods travelled on), `lines` (a list of `DeliveryLine`, the line-by-line detail), `proofOfDelivery` (→ `ProofOfDelivery`), `referencesOrder` (→ one or more `PurchaseOrder`). A delivery is not the shipment of one order: a round trip loads whatever is ready for a customer that day, and what is ready rarely belongs to a single order — a note commonly carries a few lines of one order and a few of another, while the rest of both waits for the next departure. Without `referencesOrder` the note was the one document of the cycle that could not say what it answers to: `referencesQuote` walks a quote up to its order, `referencesOrder` walks an order up to its invoice, and the delivery sat outside that chain. ⚠️ It names the orders the note touches, not how much of each was delivered: that belongs to `DeliveryLine`, one line at a time. |
| <a id="deliveryline"></a>`DeliveryLine` | `StructuredValue` | A `DeliveryNote` line: `position` (references the originating purchase-order line), `referencesOrder` (the order the line comes from), `referencesInvoice` (the invoice that bills it), `weight` (what actually left), `item`, `orderedQuantity`/`deliveredQuantity`/`backorderQuantity` (+ `backorderReason`), `batchNumber`/`serialNumbers` (optional traceability). Closes the gap confirmed by UBL (`DespatchLine`), GS1/EDIFACT, Odoo and SAP alike: without it, a delivery note can only say "a parcel shipped," not how much of what was actually delivered — a blind spot the moment a delivery is only partial. A delivery line is a fact of its own, neither a copy of the order's line nor a pointer to it: it says what left **on this note**, of that line, on that day. The quantity belongs to the delivery — which is why a line can go out in halves, and why a document that merely pointed at the order's lines could not express it. 🔑 `referencesOrder` is not a navigation convenience: as soon as a note mixes several orders, a `position` on its own names nothing — line 325 of which order? — and without it `orderedQuantity` cannot even be filled when the source states the ordered quantity on the order alone. `referencesInvoice` is its sibling, and the link lives **on the line, never on the header**: a note can be billed by more than one invoice — it delivers several orders, each invoiced on its own — so a reference on the header would have to choose between them. At the line grain the question does not arise: a line belongs to one order, hence to one invoice. `weight` says what the line **actually** weighs — the goods that left, not the goods that were ordered: a line delivering 84 of the 120 square meters ordered weighs the 84, and the lines sum to the note's `weight`. It holds the raw reference as read, or the resolved `PurchaseOrder`: the union names a single class, so `Reflection::hydrate()` resolves a joined row on its own, while a bare key is left as read. |
| <a id="proofofdelivery"></a>`ProofOfDelivery` | `StructuredValue` | The confirmation that a delivery was received: `signatory`, `date`, `discrepancyNote`. A trace, not an engine (same logic as `PaymentReminder`): signature capture and dispute resolution stay consumer-side concerns. |
| <a id="goodsreceiptconfirmation"></a>`GoodsReceiptConfirmation` | `BusinessDocument` | A goods-receipt confirmation — the buyer confirms having received a `DeliveryNote`'s goods: `referencesDeliveryNote` (→ one or more `DeliveryNote`), `lines` (a list of `GoodsReceiptLine`). This is what UBL/Peppol's `ReceiptAdvice` actually models (not to be confused with `Receipt`, which is a proof of **payment**). The first buyer-side document of an otherwise seller-centric hierarchy. |
| <a id="goodsreceiptline"></a>`GoodsReceiptLine` | `StructuredValue` | A `GoodsReceiptConfirmation` line: `position`, `item`, `expectedQuantity`/`receivedQuantity`, `condition` (state of the received goods), `discrepancyNote`. The buyer-side counterpart of `DeliveryLine`. |
| <a id="receipt"></a>`Receipt` | `BusinessDocument` | A receipt — proof that a payment was received: `confirmationNumber`, `paymentMethod`/`paymentMethodId` (reused from `org\schema\Invoice`), `referencesInvoice` (→ one or more `Invoice`, **optional**). The received amount isn't duplicated here (already covered by the inherited `totals`); the date received is the inherited `issueDate`. Two shapes: against one or more invoices (the common case), or a **direct/cash sale with no prior invoice** (`referencesInvoice` left null, the sale carried directly on the inherited `documentLines`/`taxes`/`totals` — the point-of-sale case, no dedicated type). |
| <a id="remittanceadvice"></a>`RemittanceAdvice` | `BusinessDocument` | A remittance advice — the document a **payer** sends the payee to detail a payment: `amountRemitted`, `referencesInvoice` (→ one or more `Invoice`). The payer-side counterpart of `Receipt` (seller-side): the two deliberately coexist, each modelling the same event from opposite ends of the transaction. Defined in UBL as `RemittanceAdvice`. |
| <a id="statement"></a>`Statement` | `BusinessDocument` | A statement — recaps, over a period, the documents that moved an account's balance: `billingPeriod` (reusing the name already used by `org\schema\Invoice`), `entries` (a list of `StatementEntry`), `openingBalance`/`closingBalance` (`MonetaryAmount`, no Schema.org equivalent — UBL names them `BeginningBalanceAmount`/`EndingBalanceAmount`), `totalDebit`/`totalCredit` (period aggregates, mirroring UBL `TotalDebitAmount`/`TotalCreditAmount`), `agingSummary` (→ `AgingSummary`, the aging breakdown). The only class of the lot that isn't a thin single-property subclass: it introduces its own line concept. |
| <a id="statemententry"></a>`StatementEntry` | `StructuredValue` | A `Statement` line: `document` (the related `BusinessDocument`, or a plain string when the full object isn't available), `type` (→ `StatementEntryType`: invoice, payment, credit note…, rather than inferring it from the referenced document), `date`, `dueDate` (the maturity aging is computed from), `amount` (the signed movement), `debitAmount`/`creditAmount` (an optional debit/credit split, complementing `amount`), `balance` (the running balance after this entry). Distinct from `BusinessDocumentLine`, which prices a product/service, not an account movement. |
| <a id="agingsummary"></a>`AgingSummary` | `StructuredValue` | A `Statement`'s aging breakdown: `current`, `days1To30`, `days31To60`, `days61To90`, `over90` (each a `MonetaryAmount`). A reporting convention expected of a statement of account (QuickBooks, Xero) that UBL itself doesn't carry. The library only models the shape: the consumer computes each bucket (typically from each entry's `dueDate`), this object stores the result — a value, not an aging engine (same logic as `PaymentReminder`). |
| <a id="businessdocumentexporter"></a>`BusinessDocumentExporter` | *(interface)* | The serialization contract for a `BusinessDocument`: `export(BusinessDocument $document): string`. Regulatory formats (UBL, Factur-X, Peppol…) remain out of scope for now. |
| <a id="jsonldexporter"></a>`JsonLdExporter` | `BusinessDocumentExporter` | Demonstration implementation: delegates to `ThingTrait::jsonSerialize()` (inherited via `Intangible`/`Thing`) then `json_encode()`. |

---

## Associated constants

Each class exposes its property constants through a dedicated trait under [`constants/traits/business/documents/`](../../../src/xyz/oihana/schema/constants/traits/business/documents), aggregated in [`DocumentsTrait`](../../../src/xyz/oihana/schema/constants/traits/business/DocumentsTrait.php), itself composed into [`BusinessTrait`](../../../src/xyz/oihana/schema/constants/traits/BusinessTrait.php) and then into the global [`Oihana`](../../../src/xyz/oihana/schema/constants/Oihana.php) aggregator — unlike `BusinessIdentityTrait`/`UserProfileTrait`, no name collision was found, so these constants are directly reachable via `Oihana::RATE`, `Oihana::AMOUNT`, etc., in addition to the class constants (`TaxDetail::RATE`, `Adjustment::AMOUNT`…).

---

## Related reading

- [`xyz\oihana\schema\business`](business.md) — `BusinessIdentity`, `UserProfile`.
- [`xyz\oihana\schema\products`](products.md) — `PriceComponentType`, reused by `Adjustment::$type`.
- [`xyz\oihana\schema\enumerations`](../../../src/xyz/oihana/schema/enumerations) — `BusinessDocumentAuthority`, reused by `BusinessDocument::$authority`; `PaymentReminderLevel`/`PaymentReminderChannel`, reused by `PaymentReminder`; `StatementEntryType`, reused by `StatementEntry`; `CreditNoteReasonCode`/`CreditNoteDisposition`, reused by `CreditNote`.
- [`org\schema`](../schema-org/README.md) — `MonetaryAmount`, `PriceSpecification`, `StructuredValue`.
- [Getting started](../getting-started.md) — installation, hydration, JSON-LD basics.
- [API reference](../../../docs).
