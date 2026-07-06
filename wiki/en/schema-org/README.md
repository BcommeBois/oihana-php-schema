# `org\schema` — Schema.org vocabulary

The `org\schema` namespace is the heart of the library. It provides a strongly-typed PHP implementation of the [Schema.org](https://schema.org) vocabulary — roughly **400 value objects** organised by topic.

> 🇫🇷 Cette page existe aussi en [français](../../fr/schema-org/README.md).

---

## When to use it

Use `org\schema` whenever you want to describe a *thing* with standard, web-friendly semantics — typically to:

- emit JSON-LD payloads consumable by search engines or linked-data tools,
- store structured documents in a database (ArangoDB metadata is built in),
- expose REST/GraphQL resources with consistent property names,
- migrate from ad-hoc DTOs to a shared vocabulary.

---

## Quick example

```php
use org\schema\Person;
use org\schema\PostalAddress;
use org\schema\constants\Schema;

$alice = new Person
([
    Schema::ID      => '2555' ,
    Schema::NAME    => 'Alice' ,
    Schema::EMAIL   => 'alice@example.com' ,
    Schema::ADDRESS => new PostalAddress
    ([
        Schema::STREET_ADDRESS => '2 chemin des Vergers' ,
        Schema::POSTAL_CODE    => '49170' ,
    ]),
]);

echo json_encode( $alice , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

Every entity ships its own `@type` and `@context = 'https://schema.org'` in the JSON-LD output, and null properties are stripped automatically.

---

## Browse by topic

The vocabulary is split across logical sub-namespaces. Each link below jumps to a dedicated guide with a class catalog, code example and source pointers.

| Guide                                  | Sub-namespace                | Classes | Highlights                                                |
|----------------------------------------|------------------------------|---------|-----------------------------------------------------------|
| [Core types](core.md)                  | `org\schema`                 | ~100    | `Thing`, `Person`, `Organization`, `Place`, `Event`, `Product`, `Offer`, `Order`, `Service`, `Brand`. |
| [Actions](actions.md)                  | `org\schema\actions`         | ~115    | Full Schema.org `Action` hierarchy (BuyAction, LikeAction, RegisterAction, ShareAction, …). |
| [Creative works](creative-work.md)     | `org\schema\creativeWork`    | ~60     | `Article`, `Book`, `ImageObject`, `VideoObject`, `Certification`, `Comment`. |
| [Events](events.md)                    | `org\schema\events`          | ~2      | Specialized event types beyond the top-level `Event`.     |
| [Places](places.md)                    | `org\schema\places`          | ~26     | `Country`, `City`, `Restaurant`, `Accommodation`, landmarks. |
| [Organizations](organizations.md)      | `org\schema\organizations`   | ~18     | `EducationalOrganization`, `LocalBusiness`, `MedicalOrganization`, NGOs. |
| [Services](services.md)                | `org\schema\services`        | ~7      | Financial products, payment services, currency conversion. |
| [Items / lists](items.md)              | `org\schema\items`           | ~5      | `HowToStep`, `HowToItem`, `HowToTool`, `HowToSupply`.     |
| [Enumerations](enumerations.md)        | `org\schema\enumerations`    | ~86     | `DayOfWeek`, `EventStatusType`, `ItemAvailability`, `DeliveryMethod`. |

For the exhaustive class list, browse the auto-generated [API reference](../../../docs) or the source under [`src/org/schema/`](../../../src/org/schema).

---

## Property constants

Hard-coding string keys is risky. Use the aggregator class [`org\schema\constants\Schema`](../../../src/org/schema/constants/Schema.php) instead — it exposes every property name as a typed `public const string`, grouped by topic via traits under [`org\schema\constants\traits/`](../../../src/org/schema/constants/traits).

```php
use org\schema\constants\Schema;

// IDE-friendly, refactor-safe property keys:
Schema::NAME            // 'name'
Schema::AT_TYPE         // '@type'
Schema::AT_CONTEXT      // '@context'
Schema::ADDRESS         // 'address'
Schema::STREET_ADDRESS  // 'streetAddress'
```

The same `Prop` alias is also available for shorter snippets:

```php
use org\schema\constants\Prop;

Prop::NAME; // 'name'
```

---

## Related reading

- [Getting started](../getting-started.md) — installation, hydration and JSON-LD basics.
- [`xyz\oihana\schema`](../oihana/core.md) — Oihana cross-cutting utilities (Pagination, Log, AuditAction).
- [Schema.org official website](https://schema.org) — original specification.
- [API reference](../../../docs) — every class, property and method.
