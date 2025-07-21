# Oihana PHP - Schema

![Oihana PHP Schema](https://raw.githubusercontent.com/BcommeBois/oihana-php-schema/refs/heads/main/assets/images/oihana-php-schema-logo-inline-512x160.png)

Oihana Schema is a PHP library that provides an object-oriented implementation of the [Schema.org](https://www.schema.org) vocabulary. 
It is designed to encapsulate structured data using strongly typed value objects, with automatic serialization and hydration features.

This library is ideal for representing database records or REST API resources in a structured, semantically rich way, compatible with JSON-LD and linked data ecosystems.

## ✨ Key Features

 - ✔️ Full modeling of Schema.org entities
 - 🧩 Automatic JSON-LD serialization (JsonSerializable)
 - 🪄 Recursive object hydration (including nested types and union types)
 - 🧠 Internal reflection system (oihana\reflections)
 - 🎯 Safe property access via constants (e.g. Prop::NAME)
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
use org\schema\constants\Prop;

$person = new Person
([
    Prop::ID      => '2555',
    Prop::NAME    => 'John Doe',
    Prop::ADDRESS => new PostalAddress
    ([
        Prop::STREET_ADDRESS => '2 chemin des Vergers',
        Prop::POSTAL_CODE    => '49170'
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

The org\schema\constants\Prop class contains constant names for every property in the **Schema.org** ontology and its extensions:

```php
use org\schema\constants\Prop;

$event = new Event
([
    Prop::NAME     => 'Oihana Conf 2025',
    Prop::LOCATION => new Place([ Prop::NAME => 'Nantes' ])
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

## ✅ Running Unit Tests

To run all tests:
```bash
composer run-script test
```

To run a specific test file:
```bash
composer run test ./tests/schema/ThingTest.php
```

## 🧾 Licence

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