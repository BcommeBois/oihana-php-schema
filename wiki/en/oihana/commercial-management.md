# Commercial management with Oihana

This guide explains **the commercial management cycle** the library models — from the first proposal made to a customer all the way to the end-of-period account statement — and clarifies **what distinguishes each document**: what a quote is for, how a purchase order differs from an invoice, why a credit note is not a debit note, and so on.

This is the **business-level** read, a companion to the [`business-documents` technical reference](business-documents.md), which details each class's properties and hydration. Here we tell the *why* and the *sequence*; there you find the *how*.

> 🇫🇷 Cette page existe aussi en [français](../../fr/oihana/gestion-commerciale.md).

---

## The cycle at a glance

A commercial transaction almost always follows the same arc: you propose, you order, you deliver, you invoice, you get paid — and you correct or recap when needed. The library models each step with its own document, all descending from a common parent, [`BusinessDocument`](business-documents.md#businessdocument).

```
        SELLER ──────────────────────────────────────────────► BUYER
           │                                                      │
   1.  Quote  ────────────────── proposal ──────────────────────► │
           │                                                      │
           │ ◄─────────────── 2. PurchaseOrder ───────────────────┤ commitment
           │                                                      │
   3.  DeliveryNote ──────────── goods shipped ─────────────────► │
           │                                                      │
           │ ◄──── 4. GoodsReceiptConfirmation ───────────────────┤
           │                                                      │
   5.  Invoice ──────────────────── claim ──────────────────────► │
           │                                                      │
           │      ± CreditNote / DebitNote                        │ correction
           │                                                      │
           │ ◄─────────────── 6. Payment ─────────────────────────┤
   7.  Receipt ◄──────────────────► RemittanceAdvice
           │                                                      │
   8.  Statement ──────────── period recap ─────────────────────► │
```

Not every step is mandatory: a cash sale can jump straight to the receipt, a service can be invoiced with no delivery note. The cycle is a **template**, not a constraint — each document exists independently and links to the others when relevant.

---

## Who issues what, when, and what it commits

This is the reference table for telling the documents apart. The **commits** column gives each piece's weight: mere information, a revocable proposal, or a firm commitment.

| # | Document | Class | Issued by | When | What it commits |
|---|----------|-------|-----------|------|-----------------|
| 1 | **Quote** | [`Quote`](business-documents.md#quote) | Seller | Before any order | A price **proposal**, valid until a date (`validThrough`). Doesn't commit the buyer; commits the seller to the price while the quote is valid. |
| 2 | **Purchase order** | [`PurchaseOrder`](business-documents.md#purchaseorder) | Buyer | After accepting the quote | The buyer's **firm commitment** to buy. References the originating quote(s) (`referencesQuote`). |
| 3 | **Delivery note** | [`DeliveryNote`](business-documents.md#deliverynote) | Seller | On shipping the goods | Attests **what was shipped** (delivered vs. ordered quantity, per line). Claims no payment. |
| 4 | **Goods-receipt confirmation** | [`GoodsReceiptConfirmation`](business-documents.md#goodsreceiptconfirmation) | Buyer | On receiving the goods | Attests **what was received** (quantities, condition, discrepancies). The basis for a possible credit note or dispute. |
| 5 | **Invoice** | [`Invoice`](business-documents.md#invoice) | Seller | After delivery / service | A payable **claim**: the amount due, its due date, its payment status. References the purchase order(s) (`referencesOrder`). |
| 6a | **Credit note** | [`CreditNote`](business-documents.md#creditnote) | Seller | After invoicing, downward correction | **Reduces** what's owed (return, pricing error, goodwill). References the corrected invoice(s). |
| 6b | **Debit note** | [`DebitNote`](business-documents.md#debitnote) | Seller | After invoicing, upward correction | **Increases** what's owed (under-billing, missing line). The symmetric inverse of the credit note. |
| 7a | **Receipt** | [`Receipt`](business-documents.md#receipt) | Seller | On receiving payment | The **seller-side proof** that a payment was received. |
| 7b | **Remittance advice** | [`RemittanceAdvice`](business-documents.md#remittanceadvice) | Buyer (payer) | On issuing payment | The **payer-side detail** of a payment: which invoices it settles, for what amount. |
| 8 | **Account statement** | [`Statement`](business-documents.md#statement) | Seller | Periodically | The **recap** of an account's movements over a period: opening and closing balance, aging breakdown. |

---

## Seller or buyer: two mirror viewpoints

The hierarchy was long designed from the **seller's** viewpoint alone (the party that proposes, delivers, invoices, collects). But several documents have a **buyer-side counterpart** describing the same event from the other end of the transaction:

| Event | Seen by the seller | Seen by the buyer |
|-------|--------------------|-------------------|
| Goods change hands | [`DeliveryNote`](business-documents.md#deliverynote) — "here's what I'm shipping" | [`GoodsReceiptConfirmation`](business-documents.md#goodsreceiptconfirmation) — "here's what I received" |
| Money changes hands | [`Receipt`](business-documents.md#receipt) — "I received your payment" | [`RemittanceAdvice`](business-documents.md#remittanceadvice) — "here's the payment I'm sending you" |

The two versions **coexist deliberately**: software acting for the seller models its view, buyer-side software models its own, neither overloading the other's type. Anyone needing only one side simply ignores the other.

On every document, both parties are always named the same way — `customer` and `seller`, the names inherited from Schema.org — whichever way you read it.

To state explicitly **which side you stand on**, any `BusinessDocument` can carry a `direction` property (→ [`BusinessDocumentDirection`](core.md#businessdocumentdirection)): `SALE` when the operator is the seller (an outbound, sales document), `PURCHASE` when it is the customer (an inbound, procurement document). It is **orthogonal** to the document's type and to its lifecycle status: it only tells which of the `seller`/`customer` parties is the operator's own organization — enough to let one model serve the seller's or the buyer's view interchangeably.

---

## The thread linking the documents

Each link references its upstream through a `references*` property, so a transaction's whole chain can be walked back:

```
Quote ◄── referencesQuote ── PurchaseOrder ◄── referencesOrder ── Invoice ◄── referencesInvoice ── CreditNote / DebitNote / Receipt / RemittanceAdvice
                                                                    │
DeliveryNote ◄── referencesDeliveryNote ── GoodsReceiptConfirmation │
```

These links are **collections**: a document may reference several. That's the reality on the ground — a consolidated invoice carries several purchase orders, a single payment settles several invoices, an order may aggregate several accepted quotes. See the [technical reference](business-documents.md#quick-example) for hydrating these links.

---

## Status: where a document is in its lifecycle

Whatever its type, every document carries a **lifecycle status** ([`BusinessDocumentStatus`](core.md#businessdocumentstatus)), distinct from an invoice's *payment* status. It describes where the piece itself stands:

| Status | Meaning |
|--------|---------|
| `DRAFT` | Being prepared, not yet sent. |
| `SENT` | Transmitted to the recipient. |
| `ACCEPTED` | Accepted by the recipient (a quote turned into an order, say). |
| `REJECTED` | Rejected by the recipient. |
| `EXPIRED` | Validity period elapsed (a quote past its date). |
| `CONVERTED` | Turned into another document (a quote converted to an order). |
| `CANCELLED` | Cancelled after being issued. |

This lifecycle is **generic**: it applies to a quote as much as to an invoice. Fine-grained financial tracking (is an installment paid, overdue?) is a separate mechanism — see below.

---

## Financial tracking: installments, reminders, collection

Beyond the documents themselves, the library tools up **payment tracking**:

- A **payment schedule** ([`PaymentSchedule`](business-documents.md#paymentschedule)) spreads a settlement over several **installments** ([`PaymentInstallment`](business-documents.md#paymentinstallment)), each with its date, amount and own payment status (paid, due, overdue).
- An installment or a schedule can carry its **reminders** ([`PaymentReminder`](business-documents.md#paymentreminder)) — the record of dunning notices sent to the customer, with their escalation level (from a courteous reminder to a formal notice) and any late-payment charges.
- The **account statement** ([`Statement`](business-documents.md#statement)) recaps an account's movements and exposes an **aging breakdown** ([`AgingSummary`](business-documents.md#agingsummary)) — the balance split by days overdue (current, 1–30, 31–60, etc.), the collection-steering tool.

One principle runs through this whole layer: the library **records**, it does not **decide**. It models what a reminder or an aging breakdown *is*; it holds neither the logic deciding *when* to remind, nor the computation filling the aging buckets. Those rules stay the responsibility of the application consuming the library.

---

## Correcting an invoice: credit note or debit note

An issued invoice is never edited — it is **corrected by a new document**, which preserves the audit trail:

- to **reduce** what's owed (returned goods, pricing error, goodwill): a **credit note** ([`CreditNote`](business-documents.md#creditnote));
- to **increase** what's owed (under-billing, extra work): a **debit note** ([`DebitNote`](business-documents.md#debitnote)).

In both cases the correcting amount flows through the inherited monetary recap (`totals`), always positive: it's the **document type** that carries the meaning ("this reduces" or "this increases"), never a sign convention hidden in the amounts. A credit note may be only partially used — its not-yet-applied part is carried by `remainingBalance`, and its fate (refunded, reapplied, pending) by `disposition`.

---

## Related reading

- [Business documents](business-documents.md) — the technical reference: class catalog, properties, hydration examples.
- [Why an ontology](../why-an-ontology.md) — the vision: why build on Schema.org and extend it rather than start from scratch.
- [Commerce layer](products.md) — `Product`, `PriceComponentType` (reused by adjustments), units of sale.
- [Business entities](organizations.md) / [People](people.md) — the parties of a transaction: `Customer`, `Provider`, `Seller`, `Company`.
- [Getting started](../getting-started.md) — installation, hydration, JSON-LD basics.
