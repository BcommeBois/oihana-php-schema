# Oihana PHP - Schema

![Oihana PHP Schema](https://raw.githubusercontent.com/BcommeBois/oihana-php-schema/refs/heads/main/assets/images/oihana-php-schema-logo-inline-512x160.png)

Oihana Schema is a PHP library that provides an object-oriented implementation of the [Schema.org](https://www.schema.org) vocabulary. 
It is designed to encapsulate structured data using strongly typed value objects, with automatic serialization and hydration features.

[![Latest Version](https://img.shields.io/packagist/v/oihana/php-schema.svg?style=flat-square)](https://packagist.org/packages/oihana/php-schema)  
[![Total Downloads](https://img.shields.io/packagist/dt/oihana/php-schema.svg?style=flat-square)](https://packagist.org/packages/oihana/php-schema)  
[![License](https://img.shields.io/packagist/l/oihana/php-schema.svg?style=flat-square)](LICENSE)

This library is ideal for representing database records or REST API resources in a structured, semantically rich way, compatible with JSON-LD and linked data ecosystems.

## ✨ Key Features

 - ✔️ Full modeling of Schema.org entities
 - 🧩 Automatic JSON-LD serialization (JsonSerializable)
 - 🪄 Recursive object hydration (including nested types and union types)
 - 🧠 Internal reflection system (oihana\reflections)
 - 🎯 Safe property access via constants (e.g. Schema::NAME)
 - 📚 Extensible architecture for custom ontologies
 - 🔐 Support for ArangoDB metadata (_id, _key, _rev, _from, _to)

## 📦 Installation

> **Requires [PHP 8.4+](https://php.net/releases/)**

Install via [Composer](https://getcomposer.org):
```bash
composer require oihana/php-schema
```

## 🚀 Quick Example

Simple usage
```php
use org\schema\Person;
use org\schema\PostalAddress;
use org\schema\constants\Schema;

$person = new Person
([
    Schema::ID      => '2555',
    Schema::NAME    => 'John Doe',
    Schema::ADDRESS => new PostalAddress
    ([
        Schema::STREET_ADDRESS => '2 chemin des Vergers',
        Schema::POSTAL_CODE    => '49170'
    ])
]);

echo json_encode( $person , JSON_PRETTY_PRINT ) ;
```

JSON-LD output
```json
{
  "@type": "Person",
  "@context": "https://schema.org",
  "id": "2555",
  "name": "John Doe",
  "address": {
    "@type": "PostalAddress",
    "@context": "https://schema.org",
    "streetAddress": "2 chemin des Vergers",
    "postalCode": "49170"
  }
}
```

## 🗂️ Schemas overview

The library exposes three top-level namespaces. Each row below links to a dedicated wiki guide (English / Français) that lists the classes, gives a code example and points to the source.

| Namespace | What it covers | Classes | Wiki guide |
|-----------|----------------|---------|------------|
| `org\schema` | Full Schema.org vocabulary — typed value objects for `Thing`, `Person`, `Place`, `Event`, `Product`, `Offer`, the complete `Action` hierarchy, creative works, organizations, services, enumerations. | ~400 | [🇬🇧 EN](wiki/en/schema-org/README.md) · [🇫🇷 FR](wiki/fr/schema-org/README.md) |
| `xyz\oihana\schema\auth` | OAuth2/OIDC clients, sessions, keyfiles, users, roles, permissions, RBAC policies, Casbin helpers, JWT claims registry. | 15 | [🇬🇧 EN](wiki/en/oihana/auth.md) · [🇫🇷 FR](wiki/fr/oihana/auth.md) |
| `xyz\oihana\schema\business` | Account ↔ business-world link: `BusinessIdentity` (typed account/entity link) and `UserProfile` (creation-time provisioning template). | 2 | [🇬🇧 EN](wiki/en/oihana/business.md) · [🇫🇷 FR](wiki/fr/oihana/business.md) |
| `xyz\oihana\schema\business\documents` | The quote → purchase order → invoice cycle (and its neighbors): `BusinessDocument` (common parent), `Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `DebitNote`, `DeliveryNote`, `GoodsReceiptConfirmation`, `Receipt`, `RemittanceAdvice`, `Statement`/`StatementEntry`, the `BusinessDocumentExporter`/`JsonLdExporter` export layer, plus the cross-cutting value objects `TaxDetail`, `Adjustment`, `EcoFeeRule`/`AppliedEcoFee`, `DocumentTotals`, `BusinessDocumentLine`, `PaymentSchedule`/`PaymentInstallment`/`PaymentReminder`, `DeliveryLine`/`ProofOfDelivery`, `GoodsReceiptLine`, `AgingSummary`. | 27 | [🇬🇧 EN](wiki/en/oihana/business-documents.md) · [🇫🇷 FR](wiki/fr/oihana/business-documents.md) |
| `xyz\oihana\schema\http` | Structured HTTP request metadata: `UserAgentInfo` (browser, OS, device class, bot flag). | 1 | [🇬🇧 EN](wiki/en/oihana/http.md) · [🇫🇷 FR](wiki/fr/oihana/http.md) |
| `xyz\oihana\schema\organizations` | Business entities: `Company` (French SIRET/APE identifiers, address + contact ingestion) and its `Customer`, `Provider`, `Subsidiary`, `Affiliate` flavors. | 5 | [🇬🇧 EN](wiki/en/oihana/organizations.md) · [🇫🇷 FR](wiki/fr/oihana/organizations.md) |
| `xyz\oihana\schema\people` | Business contacts: `Person` and its typed flavors `Seller`, `CustomerEmployee`, `Employee`, `ProviderEmployee`, `SubsidiaryEmployee`. | 6 | [🇬🇧 EN](wiki/en/oihana/people.md) · [🇫🇷 FR](wiki/fr/oihana/people.md) |
| `xyz\oihana\schema\products` | Commerce layer: `Product` (eligible-quantity tree, unit-of-sale conversions, `resolveUnitCode()` hook) with `StockLevel`, `TaxRate`, `PriceSegmentation`, payment conditions, provider/warehouse information. | 13 | [🇬🇧 EN](wiki/en/oihana/products.md) · [🇫🇷 FR](wiki/fr/oihana/products.md) |
| `xyz\oihana\schema\places` | Operational locations: `Site`, `Office`, `Warehouse`, `JobSite`. | 4 | [🇬🇧 EN](wiki/en/oihana/places.md) · [🇫🇷 FR](wiki/fr/oihana/places.md) |
| `xyz\oihana\schema\thesaurus` | SKOS concept trees on top of `DefinedTerm`: `ThesaurusTerm`, `Concept`, `ProductCategoryTerm`, `ConceptScheme`, `Collection`, `OrderedCollection` — `broader`/`narrower` hierarchy, notes, cross-scheme mappings, collections — plus the registry layer (`ThesaurusScheme`, `ThesaurusDomain`). | 8 | [🇬🇧 EN](wiki/en/oihana/thesaurus.md) · [🇫🇷 FR](wiki/fr/oihana/thesaurus.md) |
| `xyz\oihana\schema` | Cross-cutting Oihana types: `Pagination`, `Log`, `AuditAction`, audit enumerations. | 3 + enums | [🇬🇧 EN](wiki/en/oihana/core.md) · [🇫🇷 FR](wiki/fr/oihana/core.md) |
| `com\progress\schema` | OpenEdge Progress SQL `SYS%` system catalog: tables, columns, indexes, views, users, privileges, constraints, sequences, triggers, procedures, data types. | 16 | [🇬🇧 EN](wiki/en/openedge-progress.md) · [🇫🇷 FR](wiki/fr/openedge-progress.md) |

Every entity extends `org\schema\Thing`, so they all share the same JSON-LD serialization, hydration and ArangoDB metadata. Sub-namespaces override the `CONTEXT` constant so downstream consumers can tell them apart:

| Namespace                | JSON-LD `@context`                |
|--------------------------|-----------------------------------|
| `org\schema`             | `https://schema.org`              |
| `xyz\oihana\schema*`     | `https://schema.oihana.xyz`       |
| `com\progress\schema`    | `https://schema.progress.com`     |

## 🧠 Internal Architecture

### Base class: Thing

All entities extend the base class org\schema\Thing, which includes common Schema.org and metadata properties, as well as serialization logic:

The ThingTrait handles:
- Dynamic constructor from arrays or objects
- JSON-LD serialization via jsonSerialize()
- Reflection-based helpers from ReflectionTrait

#### Recursive Hydration

The internal **[Reflection::hydrate()](https://github.com/BcommeBois/oihana-php-system/blob/00552f088022ee2af836b011d0efc0c69ffeab63/src/oihana/reflections/Reflection.php#L42)** method builds full object graphs from associative arrays, including nested value objects and union types:

```php
$person = $reflection->hydrate
([
    'name'    => 'Alice',
    'address' => [ 'streetAddress' => '123 Lilac Street' ]
], Person::class ) ;
````

## 🔐 Safe Property Access

The org\schema\constants\Schema class contains constant names for every property in the **Schema.org** ontology and its extensions:

```php
use org\schema\constants\Schema;
use org\schema\Event;

$event = new Event
([
    Schema::NAME     => 'Oihana Conf 2025',
    Schema::LOCATION => new Place([ Schema::NAME => 'Nantes' ])
]);
```

Properties are grouped by logical trait namespaces (e.g. Thing, Person, Event, etc.) for auto-completion and modularity:

```php
trait Thing 
{
    const string NAME = 'name';
    const string URL  = 'url';
    const string ID   = 'id';
    // ...
}
```

## 📚 Documentation

Two complementary sets of documentation are available:

- 📖 **Auto-generated API reference** (every class, property and method) — published at https://bcommebois.github.io/oihana-php-schema. Regenerate it locally with `composer doc`.
- ✍️ **Hand-written wiki** (concepts, guides, walkthroughs) — bilingual EN/FR under [`wiki/`](wiki/README.md):
  - 🇬🇧 [`wiki/en/README.md`](wiki/en/README.md) — English guides ([Getting started](wiki/en/getting-started.md))
  - 🇫🇷 [`wiki/fr/README.md`](wiki/fr/README.md) — Guides en français ([Démarrage rapide](wiki/fr/demarrage.md))

While the wiki grows, you can also explore the following namespaces directly:
- `org\schema\` for value objects
- `org\schema\traits` for logic traits
- `org\schema\constants` for property constants

## ✅ Running Unit Tests

To run all tests:
```bash
composer test
```

To run a specific test file:
```bash
composer test ./tests/org/schema/ThingTest.php
composer test ./tests/xyz/oihana/schema/PaginationTest.php
```

## 🧾 License

This project is licensed under the [Mozilla Public License 2.0 (MPL-2.0)](https://www.mozilla.org/en-US/MPL/2.0/).

## 👤 About the author

* Author : Marc ALCARAZ (aka eKameleon)
* Mail : marc@ooop.fr
* Website : http://www.ooop.fr

## 🛠️ Generate the Documentation

We use [phpDocumentor](https://phpdoc.org/) to generate the documentation into the ./docs folder.

### Usage
Run the command :
```bash
composer doc
```

## 🧩 Advanced Usage

### Union-typed properties
Some properties accept multiple types. For instance, `publisher` may be a `string`, a `Person`, or an `Organization`.
```php
use org\schema\CreativeWork;
use org\schema\Person;
use org\schema\Organization;
use org\schema\constants\Schema;

$post = new CreativeWork
([
    Schema::NAME      => 'Release Notes',
    Schema::PUBLISHER => new Organization([ Schema::NAME => 'Oihana' ])
]);
```

### Arrays and nested entities
You can compose objects with arrays of other entities, leveraging the public-typed properties.
```php
use org\schema\Thing;
use org\schema\constants\Schema;

$parent = new Thing
([
    Schema::NAME   => 'Bundle',
    Schema::HAS_PART => 
    [
        new Thing([ Schema::NAME => 'Part A' ]),
        new Thing([ Schema::NAME => 'Part B' ]),
    ],
]);
```

### JSON-LD metadata for ArangoDB
Base `Thing` supports ArangoDB-style metadata fields to facilitate graph storage:
`_key`, `_id`, `_rev`, `_from`, `_to`.
```php
use org\schema\Thing;

$edge = new Thing
([
    '_from' => 'users/2555',
    '_to'   => 'groups/42',
]);
```

### Deep/recursive hydration
The constructor copies provided values into public properties. 

For deep graphs and automatic casting, you can rely on the internal reflection utilities exposed by `oihana/php-reflect` (see developer docs). A typical approach is to call a reflection-based `hydrate()` to materialize nested arrays into value objects.

## 🧱 Extending the Library

Define your own types by extending `org\schema\Thing` or a more specific class, and add your public-typed properties. You can also define constants alongside `org\schema\constants\Schema` for safer access.
```php
namespace app\domain;

use org\schema\Thing;

class CustomAsset extends Thing
{
    public ?string $slug;
    public ?string $category;
}
```

## ⚙️ Installation Notes

- This package requires PHP 8.4+.
- It depends on `oihana/php-core` and `oihana/php-reflect`. If your project enforces stable versions only, you may need to allow dev versions while these libraries are pre-release:
  - In your root composer.json: set `"minimum-stability": "dev"` and `"prefer-stable": true` if needed.

## 🤝 Contributing

Contributions are welcome! Please:
- Open an issue to discuss significant changes before submitting a PR.
- Add tests when fixing a bug or adding a feature.
- Keep code style consistent and types explicit.

Local setup:
```bash
composer install
composer test
composer doc
```

## 🔒 Security

If you discover a security vulnerability, please email `marc@ooop.fr`. Do not open a public issue for security reports.

## 📜 Changelog

See `CHANGELOG.md` for notable changes.

## 🔗 Related Packages

- [oihana/php-core](https://github.com/BcommeBois/oihana-php-core) – core helpers and utilities used by this library
- [oihana/php-reflect](https://github.com/BcommeBois/oihana-php-reflect) – reflection and hydration utilities

## ❓ FAQ

- Why JSON-LD?  
  It’s a web-native, schema-friendly format that plays well with linked data and search engines.
- Can I output plain arrays?  
  Yes, `jsonSerialize()` returns arrays that you can pass to any JSON encoder.
- How to ignore nulls?  
  Serialization automatically removes null values.

## 🧮 JSON Schema Generation

Generate JSON Schemas from the typed public properties of your classes.

- Single class (example: Place):
```bash
composer schema:place
```

- All classes under `src/org/schema`:
```bash
composer schemas:all
```

Details:
- Schemas are written to `schemas/*.schema.json`.
- Union types are represented as `oneOf`; class types are emitted as `$ref` into local `$defs`.
- Requires Composer autoload (run `composer dump-autoload -o` if classes are not found).

### Output layout and cleanup

- Namespaces map to folders under `schemas/`:
  - `org\schema\...` → `schemas/org/schema/.../*.schema.json`
  - `xyz\oihana\schema\...` → `schemas/xyz/oihana/schema/.../*.schema.json`
- Running `composer schemas:all` first deletes previous `*.schema.json` under `schemas/` to avoid stale files, then regenerates everything.

### Array unions handling

When a property type includes `array` plus other types (e.g. `string|ImageObject|array<ImageObject|string>|null`), the generator emits:
- direct options for `string`, `ImageObject`, `null` (if present)
- and an `array` variant whose `items` use a `oneOf` of the non-array types (here `ImageObject` and `string`).