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

Full project documentation is available at:  
👉 https://bcommebois.github.io/oihana-php-schema

A complete developer guide will be available soon, including:
- UML diagrams
- Object maps and relationships
- Auto-generated property references

In the meantime, explore the following namespaces:	
- org\schema\ for value objects
- org\schema\traits for logic traits
- org\schema\constants for property constants

## 🇫🇷 fr\\ooop Namespace (Ooop extensions)

In addition to the Schema.org core under `org\\schema`, this library provides an extension namespace for Ooop-specific needs used in the French ecosystem: `fr\\ooop\\schema`.

What you will find there:
- Value objects for domain-specific concepts (e.g., `fr\\ooop\\schema\\Pagination`).
- A constants container `fr\\ooop\\schema\\constants\\Ooop` exposing safe property names via traits (e.g., `Pagination` constants).

Example: model pagination parameters with JSON-LD support
```php
use fr\ooop\schema\Pagination;
use fr\ooop\schema\constants\Ooop;

$pagination = new Pagination
([
    Ooop::LIMIT => 50,
    Ooop::PAGE  => 2,
]);

// JSON-LD with a dedicated context for Ooop extensions
echo json_encode($pagination, JSON_UNESCAPED_SLASHES);
// {"@type":"Pagination","@context":"https://schema.ooop.fr","limit":50,"page":2}
```

Key points:
- `Pagination` extends `org\schema\Intangible` and integrates seamlessly with the rest of the model.
- `Pagination::CONTEXT` defaults to `https://schema.ooop.fr` to distinguish Ooop extensions.
- Use `fr\ooop\schema\constants\Ooop` to reference property names safely: `Ooop::LIMIT`, `Ooop::OFFSET`, `Ooop::MAX_LIMIT`, `Ooop::MIN_LIMIT`, `Ooop::NUMBER_OF_PAGES`, `Ooop::PAGE`.

## ✅ Running Unit Tests

To run all tests:
```bash
composer test
```

To run a specific test file:
```bash
composer test ./tests/org/schema/ThingTest.php
composer test ./tests/fr/ooop/schema/PaginationTest.php
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
The constructor copies provided values into public properties. For deep graphs and automatic casting, you can rely on the internal reflection utilities exposed by `oihana/php-reflect` (see developer docs). A typical approach is to call a reflection-based `hydrate()` to materialize nested arrays into value objects.

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