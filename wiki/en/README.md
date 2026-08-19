# Oihana PHP Schema — English documentation

Welcome to the English-language guides for **Oihana PHP Schema**.

This wiki is a hand-written companion to the auto-generated [API reference](../../docs). It explains the *why* behind the architecture and walks you through the most common workflows.

> 🇫🇷 Cette page existe aussi en [français](../fr/README.md).

---

## 📚 Table of contents

### Fundamentals

- [Getting started](getting-started.md) — install, hydrate your first `Thing`, serialize it to JSON-LD, use property constants safely.
- [Why an ontology](why-an-ontology.md) — the vision: why model on Schema.org and extend it, for business solutions that are both standardized and evolvable.

### Schemas by namespace

| Namespace                              | Guide                                       | What it covers                                                                              |
|----------------------------------------|---------------------------------------------|---------------------------------------------------------------------------------------------|
| [`org\schema`](schema-org/README.md)           | [Schema.org vocabulary](schema-org/README.md)      | ~400 typed value objects for Schema.org — `Thing`, `Person`, `Place`, `Event`, `Product`, `Offer`, full `Action` hierarchy, … |
| [`xyz\oihana\schema\appointments`](oihana/appointments.md) | [Appointments](oihana/appointments.md) | A meeting arranged with a customer and what gets written about it: `Appointment` (an `Event` — two axes of state, the slot and the meeting), `VisitReport` (mood, outcome, topics), `FollowUp` (what is still owed, by when). |
| [`xyz\oihana\schema\auth`](oihana/auth.md) | [Authentication & RBAC](oihana/auth.md)    | OAuth2/OIDC, sessions, keyfiles, users, roles, permissions, Casbin-friendly policies.       |
| [`xyz\oihana\schema\business`](oihana/business.md) | [Oihana business](oihana/business.md) | `BusinessIdentity` (typed account ↔ entity link) and `UserProfile` (creation-time provisioning template). |
| [`xyz\oihana\schema\organizations`](oihana/organizations.md) | [Business entities](oihana/organizations.md) | `Company` (French SIRET/APE, address + contact ingestion) and its flavors `Customer`, `Provider`, `Subsidiary`, `Affiliate`. |
| [`xyz\oihana\schema\people`](oihana/people.md) | [People](oihana/people.md)                     | `Person` and its typed flavors `Seller`, `CustomerEmployee`, `Employee`, `ProviderEmployee`, `SubsidiaryEmployee`. |
| [`xyz\oihana\schema\products`](oihana/products.md) | [Commerce layer](oihana/products.md)       | `Product` (eligible-quantity tree, unit-of-sale conversions, `resolveUnitCode()` hook) and its satellites: `StockLevel`, `TaxRate`, `PriceSegmentation`, payment conditions, provider/warehouse information. |
| [`xyz\oihana\schema\http`](oihana/http.md) | [Oihana HTTP](oihana/http.md)          | Structured HTTP request metadata: `UserAgentInfo` (browser, OS, device class, bot flag).    |
| [`xyz\oihana\schema\places`](oihana/places.md) | [Oihana places](oihana/places.md)      | Operational locations: `Site`, `Office`, `Warehouse`, `JobSite`.                            |
| [`xyz\oihana\schema\shipping`](oihana/shipping.md) | [Oihana shipping](oihana/shipping.md)  | Standing shipping arrangements: `DeliveryRouteAssignment` — which route serves an address, on which days. |
| [`xyz\oihana\schema\statistics`](oihana/statistics.md) | [Oihana statistics](oihana/statistics.md) | Bodies of already-measured figures: `Statistics`, `ObservationSeries`, `CustomerStatistics`, `ProviderStatistics` — what a counterparty traded over a year, month by month. |
| [`xyz\oihana\schema\thesaurus`](oihana/thesaurus.md) | [Oihana thesaurus (SKOS)](oihana/thesaurus.md) | SKOS concept trees: `ThesaurusTerm`, `Concept`, `ProductCategoryTerm`, `ConceptScheme`, `Collection`, `OrderedCollection` — hierarchy, notes, mappings, collections — the priced `DeliveryMethodTerm` and the recurring `DeliveryRouteTerm`, plus the registry layer (`ThesaurusScheme`, `ThesaurusDomain`). |
| [`xyz\oihana\schema`](oihana/core.md)   | [Cross-cutting Oihana types](oihana/core.md)| `Pagination`, `Log`, `AuditAction`, audit enumerations.                                     |
| [`com\progress\schema`](openedge-progress.md) | [OpenEdge Progress SQL catalog](openedge-progress.md) | `SYS%` system catalog tables: tables, columns, indexes, views, users, privileges, constraints, sequences, triggers, procedures, data types. |

### Cross-cutting mechanisms

- [Ingestion](oihana/ingestion.md) — the `__set` bridges hydrating a flat dataset row into a nested object (address, contacts, geolocation, additional properties).

### Helper functions

- [Hydration and pivots](oihana/helpers.md) — the autoloaded free functions: hydrate a raw piece of data into a typed schema object (nested references included) and resolve the business identities of an account into its scoping keys.

### Coming soon

Pages on the following topics will be added over time:

- Generating JSON Schema from typed properties (`composer schemas:all`).
- Extending the library with your own domain types.
- Hydration deep-dive: union types, nested objects, ArangoDB metadata.

---

## 🔗 Useful links

- 📖 [Auto-generated API reference](../../docs) — every class, property and method.
- 🧱 [`README.md`](../../README.md) — repository overview and quick example.
- 📝 [`CHANGELOG.md`](../../CHANGELOG.md) — notable changes between versions.
- 🐙 [GitHub repository](https://github.com/BcommeBois/oihana-php-schema)
