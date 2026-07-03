# Oihana PHP Schema — English documentation

Welcome to the English-language guides for **Oihana PHP Schema**.

This wiki is a hand-written companion to the auto-generated [API reference](../../docs). It explains the *why* behind the architecture and walks you through the most common workflows.

> 🇫🇷 Cette page existe aussi en [français](../fr/README.md).

---

## 📚 Table of contents

### Fundamentals

- [Getting started](getting-started.md) — install, hydrate your first `Thing`, serialize it to JSON-LD, use property constants safely.

### Schemas by namespace

| Namespace                              | Guide                                       | What it covers                                                                              |
|----------------------------------------|---------------------------------------------|---------------------------------------------------------------------------------------------|
| [`org\schema`](schema-org/README.md)           | [Schema.org vocabulary](schema-org/README.md)      | ~400 typed value objects for Schema.org — `Thing`, `Person`, `Place`, `Event`, `Product`, `Offer`, full `Action` hierarchy, … |
| [`xyz\oihana\schema\auth`](oihana-auth.md) | [Authentication & RBAC](oihana-auth.md)    | OAuth2/OIDC, sessions, keyfiles, users, roles, permissions, Casbin-friendly policies.       |
| [`xyz\oihana\schema\business`](oihana-business.md) | [Oihana business](oihana-business.md) | `BusinessIdentity` (typed account ↔ entity link) and `UserProfile` (creation-time provisioning template). |
| [`xyz\oihana\schema\http`](oihana-http.md) | [Oihana HTTP](oihana-http.md)          | Structured HTTP request metadata: `UserAgentInfo` (browser, OS, device class, bot flag).    |
| [`xyz\oihana\schema\places`](oihana-places.md) | [Oihana places](oihana-places.md)      | Operational locations: `Site`, `Office`, `Warehouse`, `JobSite`.                            |
| [`xyz\oihana\schema\thesaurus`](oihana-thesaurus.md) | [Oihana thesaurus (SKOS)](oihana-thesaurus.md) | SKOS concept trees: `ThesaurusTerm`, `Concept`, `ProductCategoryTerm`, `ConceptScheme`, `Collection`, `OrderedCollection` — hierarchy, notes, mappings, collections — plus the registry layer (`ThesaurusScheme`, `ThesaurusDomain`). |
| [`xyz\oihana\schema`](oihana-core.md)   | [Cross-cutting Oihana types](oihana-core.md)| `Pagination`, `Log`, `AuditAction`, audit enumerations.                                     |
| [`com\progress\schema`](openedge-progress.md) | [OpenEdge Progress SQL catalog](openedge-progress.md) | `SYS%` system catalog tables: tables, columns, indexes, views, users, privileges, constraints, sequences, triggers, procedures, data types. |

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
