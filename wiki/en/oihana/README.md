# `xyz\oihana\schema` — The Oihana extensions

The `xyz\oihana\schema` namespace is the library's **house layer**: the domain types that do not exist in Schema.org or that specialize it. It builds on the [`org\schema` vocabulary](../schema-org/README.md) — never the other way around — and stamps its documents with the `https://schema.oihana.xyz` context.

> 🇫🇷 Cette page est aussi disponible en [français](../../fr/oihana/README.md).

---

## The guides

| Guide | Namespace | Coverage |
|---|---|---|
| [Authentication & RBAC](auth.md) | `…\auth` | OAuth2/OIDC, sessions, keyfiles, users, roles, permissions, Casbin-compatible policies. |
| [Business](business.md) | `…\business` | `BusinessIdentity` (typed account ↔ entity link) and `UserProfile` (provisioning template). |
| [Business documents](business-documents.md) | `…\business\documents` | Cross-cutting value objects for the quote/order/invoice cycle: `TaxDetail`, `Adjustment`, `EcoFeeRule`/`AppliedEcoFee`, `DocumentTotals`, `BusinessDocumentLine`, `PaymentSchedule`/`PaymentInstallment`. |
| [Business entities](organizations.md) | `…\organizations` | `Company` (French SIRET/APE, address + contact ingestion) and its `Customer`, `Provider`, `Subsidiary`, `Affiliate` flavors. |
| [People](people.md) | `…\people` | `Person` and its typed flavors `Seller`, `CustomerEmployee`, `Employee`, `ProviderEmployee`, `SubsidiaryEmployee`. |
| [Commerce layer](products.md) | `…\products` | `Product` (eligible-quantity tree, conversions, `resolveUnitCode()` hook) and its satellites. |
| [Places](places.md) | `…\places` | Operational locations: `Site`, `Office`, `Warehouse`, `JobSite`. |
| [Thesaurus (SKOS)](thesaurus.md) | `…\thesaurus` | SKOS concept trees and the registry layer (`ThesaurusScheme`, `ThesaurusDomain`). |
| [HTTP](http.md) | `…\http` | Structured request metadata: `UserAgentInfo`. |
| [Cross-cutting types](core.md) | root | `Pagination`, `Log`, `AuditAction`, audit enumerations. |

## The mechanisms

| Guide | Coverage |
|---|---|
| [Ingestion](ingestion.md) | The `__set` bridges hydrating a flat row into a nested object (address, contacts, geolocation, additional properties). |
| [Helper functions](helpers.md) | The autoloaded functions: hydrators (typed re-reading of documents) and account pivots. |

---

## See also

- [Schema.org vocabulary](../schema-org/README.md) — the `org\schema` foundation everything builds on.
- [Getting started](../getting-started.md) — installation, hydration, JSON-LD serialization.
