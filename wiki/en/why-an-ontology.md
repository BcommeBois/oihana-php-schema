# Why an ontology

This page explains the **design stance** of Oihana PHP Schema: modelling the domain on an **ontology** — a shared, structured vocabulary — by building on **Schema.org** and extending it, rather than inventing an ad-hoc data model. That choice is what makes the library both **standardized** (it speaks a common language) and **evolvable** (it extends without betraying itself).

This is a **vision** page, cutting across the whole library. To see it at work on a concrete domain, read [Commercial management](oihana/commercial-management.md), which applies everything below to the quote → invoice cycle.

> 🇫🇷 Cette page existe aussi en [français](../fr/pourquoi-une-ontologie.md).

---

## The problem: everyone reinvents their model

With no shared reference, every application redefines its own tables and its own names: here a customer is `client`, there `customer`, elsewhere `buyer`; an invoice carries a `net_amount` in one project, a `total_excl_tax` in another. Each variant is defensible — and that's exactly the problem: **nothing speaks to anything else**. Making two systems talk, exporting to an interchange format, indexing for a search engine — each then demands translation work redone from scratch every time.

An **ontology** answers this: it's a *shared, explicit* vocabulary — types (`Person`, `Organization`, `Invoice`), their properties (`name`, `customer`, `totalPaymentDue`) and the relations between them. Conforming to it means speaking a language others already understand.

---

## Why Schema.org as the foundation

[Schema.org](https://schema.org) is the world's most widely deployed ontology: jointly stewarded by Google, Microsoft, Yahoo and Yandex, it describes ~800 types covering people, organizations, places, products, offers, events, actions… It's the vocabulary search engines read, assistants consume, and countless tools expect.

Building on it brings, **for free**:

- **a vast, already-thought-through coverage** — no need to redefine what a `PostalAddress`, a `MonetaryAmount` or an `Organization` is: those types exist, matured by years of use;
- **interoperability** — a document the library produces *is* a Schema.org document, readable by anything that knows the vocabulary;
- **JSON-LD** — every entity serializes to JSON-LD, the semantic-web format, with its `@type` and `@context`, at no extra effort;
- **a naming discipline** — when a concept already exists in Schema.org, you reuse **its** name rather than inventing yet another.

The library therefore provides, in the `org\schema` namespace, a **typed PHP implementation** of that vocabulary: ~400 value classes, each a faithful mirror of a Schema.org type.

---

## The pattern: an untouched mirror, a house layer on top

Schema.org is vast but **deliberately generic**: it knows nothing of the French SIRET, of an environmental fee, of a debit note, of an aging breakdown. A real business solution needs those concepts. The library adds them — without ever distorting the foundation. Hence two cleanly separated layers:

| Layer | Namespace | Role | Golden rule |
|-------|-----------|------|-------------|
| **The mirror** | `org\schema` | A faithful, typed copy of Schema.org. | Untouched — it stays an exact reproduction, predictable to anyone who knows Schema.org. |
| **The house layer** | `xyz\oihana\schema` | The business types absent from Schema.org, or that specialize it. | It **depends** on the mirror, never the reverse. |

The direction of the dependency is the key: the house layer builds on the mirror (a house `Invoice` extends `Intangible`, reuses `MonetaryAmount`…), but the mirror **never depends** on the house layer. Business concepts can thus evolve with no risk of breaking the foundation, and a reader who knows Schema.org finds `org\schema` exactly as expected.

House documents also sign themselves with a distinct context — `@context = 'https://schema.oihana.xyz'` — so that reading a JSON-LD document tells you what is standard and what is extension.

---

## Reuse names first, invent only as a last resort

The house layer's naming rule is simple: **if Schema.org already has a name for the concept, take it**. You only coin a new name for a concept genuinely absent from the standard.

- A quote has a validity-end date? Schema.org already carries `validThrough` on `Offer`/`PriceSpecification` — reuse it, rather than inventing `validUntil`.
- A document has a customer and a seller? `customer` and `seller` exist — keep them.
- A VAT breakdown, a payment schedule, an aging breakdown, on the other hand, don't exist in Schema.org — there you create, drawing on the domain's standards (UBL, Peppol) without copying their heaviness.

This discipline maximizes compatibility: the more you speak the standard's vocabulary, the more you're understood — and the fewer house names you pile up to maintain and document.

---

## What makes the library evolvable

Standardizing must not freeze. Several design choices let the house layer **extend** instead of being rewritten:

- **Free-value enumerations.** An enumeration like `PaymentReminderLevel` or `StatementEntryType` proposes members, but also accepts a free-text label, or a **subclass** adding your own values. Your project enriches without forking.
- **Hydration-tolerant typing.** A structured property is typed `null|array|X` (never a strict `?X`), because data almost always arrives first as an array (decoded JSON, a database row). The library constructs first, deep-hydrates later — the two paths coexist without friction.
- **Property constants.** Every property has its constant (`Invoice::PAYMENT_STATUS`, `Oihana::TOTALS`), aggregated into a single access point. Coding against these constants guards against typos and makes renames safe.
- **Subclassing as the natural extension.** A need specific to your business? Extend the house class, add your properties, keep everything acquired (serialization, hydration, constants). The business-documents cycle was born exactly this way: starting from Schema.org's `Invoice`/`Order`, it grew into a full hierarchy — without ever touching the mirror.

---

## Case study: the business-documents cycle

The [business-documents layer](oihana/business-documents.md) illustrates this whole approach at real scale:

1. **Start from the standard.** Schema.org already has `Invoice` and `Order`. Take them as the conceptual starting point.
2. **Anchor without distorting.** The house `BusinessDocument` extends `Intangible` (as `Invoice`/`Order` do in the mirror), rather than subclassing the mirror directly — which stays intact.
3. **Reuse names.** `customer`, `seller`, `validThrough`, `paymentStatus`, `billingPeriod`… all borrowed from Schema.org.
4. **Fill the gaps.** Tax breakdown, adjustments, payment schedules, reminders, aging breakdown, credit note, debit note, goods-receipt confirmation… — the concepts absent from the standard, drawn from UBL/Peppol.
5. **It grew without being rewritten.** The cycle went, lot after lot, from a handful of classes to nearly thirty — each addition resting on what came before, none calling the foundation or the mirror into question.

That's the concrete proof of the promise: **standardized** because anchored on Schema.org, **evolvable** because the house layer is built to extend.

---

## Related reading

- [Commercial management](oihana/commercial-management.md) — the approach applied to the quote → invoice cycle.
- [Business documents](oihana/business-documents.md) — the technical reference of the most complete business layer.
- [Schema.org vocabulary](schema-org/README.md) — the `org\schema` foundation: what the mirror covers.
- [The Oihana extensions](oihana/README.md) — a tour of the house-layer namespaces.
- [Getting started](getting-started.md) — installation, hydration, JSON-LD serialization.
