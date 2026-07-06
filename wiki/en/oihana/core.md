# `xyz\oihana\schema` — Cross-cutting Oihana types

The top of the `xyz\oihana\schema` namespace gathers the **cross-cutting Oihana extensions** that don't belong to a specialised sub-namespace: pagination metadata for collections, log entries, and auditable action records.

> 🇫🇷 Cette page existe aussi en [français](../../fr/oihana/core.md).

---

## When to use it

| Need                                              | Class                                    |
|---------------------------------------------------|------------------------------------------|
| Describe pagination of a list endpoint or query.  | [`Pagination`](#pagination)              |
| Persist or transport a structured log entry.      | [`Log`](#log)                            |
| Record an auditable action (create / update / delete / login / logout). | [`AuditAction`](#auditaction) |
| Enumerate audit action types.                     | [`AuditActionType`](../../src/xyz/oihana/schema/enumerations/AuditActionType.php) |
| Enumerate contact channel types.                  | [`ContactType`](../../src/xyz/oihana/schema/enumerations/ContactType.php) |
| Enumerate the lifecycle status of a business document. | [`BusinessDocumentStatus`](#businessdocumentstatus) |

All entities share the `@context = 'https://schema.oihana.xyz'` distinguisher.

---

## <a id="pagination"></a> `Pagination`

`Pagination` extends `org\schema\Intangible` and models everything you typically need to describe a paginated collection: `page`, `limit`, `offset`, `numberOfPages`, plus optional `minLimit` and `maxLimit` bounds.

```php
use xyz\oihana\schema\Pagination;
use xyz\oihana\schema\constants\Oihana;

$pagination = new Pagination
([
    Oihana::PAGE            => 2  ,
    Oihana::LIMIT           => 50 ,
    Oihana::NUMBER_OF_PAGES => 10 ,
    Oihana::OFFSET          => 50 ,
]);

echo json_encode( $pagination , JSON_UNESCAPED_SLASHES );
// {"@type":"Pagination","@context":"https://schema.oihana.xyz","page":2,"limit":50,"numberOfPages":10,"offset":50}
```

Property constants are exposed via `Oihana::PAGE`, `Oihana::LIMIT`, `Oihana::OFFSET`, `Oihana::NUMBER_OF_PAGES`, `Oihana::MIN_LIMIT`, `Oihana::MAX_LIMIT`.

---

## <a id="log"></a> `Log`

`Log` extends `org\schema\Thing` and represents a single log entry with `date`, `time`, `level` and `message`. It is intentionally light — the goal is to *transport* a log record across system boundaries (queue, database row, structured JSON line), not to replace a fully-featured logger.

```php
use xyz\oihana\schema\Log;

$entry = new Log
([
    'date'    => '2026-05-18' ,
    'time'    => '14:32:10' ,
    'level'   => 'INFO' ,
    'message' => 'Application started successfully.' ,
]);

echo (string) $entry;
// 2026-05-18 14:32:10 INFO Application started successfully.
```

---

## <a id="auditaction"></a> `AuditAction`

`AuditAction` is the Oihana-flavoured **auditable action** — a structured record of *who did what, when, on which target, with which outcome*. It is designed to be persisted (for example in an `audit` collection or table) and consumed by admin dashboards, security investigations and RGPD-compliant audit trails.

It carries:

- the actor and the request that triggered the action,
- a business `event` tag (created by the caller),
- a machine-readable `outcome` (success / denied / error / …),
- the action `type` (`CREATE`, `UPDATE`, `DELETE`, `ADD`, `LOGIN`, `LOGOUT`, `REJECT`) — see the [`AuditActionType`](../../src/xyz/oihana/schema/enumerations/AuditActionType.php) enumeration.

Constants for the `AuditAction` property keys are exposed via the [`AuditTrait`](../../src/xyz/oihana/schema/constants/traits/AuditTrait.php) and reachable through the global `Oihana` aggregator.

---

## <a id="businessdocumentstatus"></a> `BusinessDocumentStatus`

`BusinessDocumentStatus` enumerates the **lifecycle status** of a business document (quote, purchase order, invoice…): `DRAFT`, `SENT`, `ACCEPTED`, `REJECTED`, `EXPIRED`, `CONVERTED`, `CANCELLED`. It extends `org\schema\enumerations\StatusEnumeration` and is distinct from Schema.org's `OrderStatus`, which tracks an order's *fulfillment* status (shipped, in transit…), not the document's own lifecycle.

This enumeration lays the ground for the upcoming business-document hierarchy (`xyz\oihana\schema\business\documents`); no class consumes it yet in this release.

---

## Related reading

- [`org\schema`](../schema-org/README.md) — base classes (`Intangible`, `Thing`, `Action`).
- [`xyz\oihana\schema\auth`](auth.md) — auth-related entities often referenced by audit records.
- [Getting started](../getting-started.md) — installation, hydration, JSON-LD basics.
- [API reference](../../../docs).
