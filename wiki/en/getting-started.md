# Getting started

This guide walks you through the four pillars of **Oihana PHP Schema**:

1. installing the library,
2. instantiating and hydrating a `Thing`,
3. serializing it to JSON-LD,
4. using property constants for safer code.

> 🇫🇷 Cette page existe aussi en [français](../fr/demarrage.md).

---

## 1. Installation

Oihana PHP Schema requires **PHP 8.4 or newer**. Install it with [Composer](https://getcomposer.org):

```bash
composer require oihana/php-schema
```

The library depends on `oihana/php-core` and `oihana/php-reflect`. Both are currently distributed as `dev-main`, so if your root project enforces stable versions you may need to add the following to your `composer.json`:

```json
{
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

Once installed, three top-level namespaces are autoloaded:

| Namespace        | What it contains                                              |
|------------------|---------------------------------------------------------------|
| `org\schema`     | Strongly-typed PHP implementation of the Schema.org vocabulary. |
| `xyz\oihana`     | Oihana-specific extensions (auth, pagination, places, …).     |
| `com\progress`   | OpenEdge Progress SQL system catalog mapping (tables, columns, indexes, users, …). |

---

## 2. The `Thing` base class

Every entity exposed by the library extends `org\schema\Thing`. `Thing` provides:

- the common Schema.org metadata properties (`id`, `name`, `url`, `description`, `owner`, `image`, `sameAs`, …),
- ArangoDB-friendly metadata fields (`_id`, `_key`, `_rev`, `_from`, `_to`) for graph storage,
- a JSON-LD–aware `jsonSerialize()` implementation,
- a constructor that accepts an associative array and copies the values into public properties.

A minimal example:

```php
use org\schema\Thing;

$thing = new Thing
([
    'name' => 'Hello World',
    'url'  => 'https://example.com',
]);

echo $thing->name; // Hello World
```

Because every property is `public`, you can also set them directly:

```php
$thing       = new Thing();
$thing->name = 'Hello World';
```

---

## 3. Hydration: from arrays to value objects

### 3.1 Shallow hydration via the constructor

The constructor inherited from `ThingTrait` performs a **shallow** copy: it reads keys from the supplied array and writes them onto matching public properties, ignoring unknown keys.

```php
use org\schema\Person;

$person = new Person
([
    'name'  => 'Alice',
    'email' => 'alice@example.com',
]);
```

### 3.2 Recursive hydration via reflection

For deep object graphs (nested entities, union types, arrays of objects) you can use the reflection-based `hydrate()` utility provided by [`oihana/php-reflect`](https://github.com/BcommeBois/oihana-php-reflect):

```php
use oihana\reflect\Reflection;
use org\schema\Person;

$person = Reflection::hydrate
(
    [
        'name'    => 'Alice',
        'address' =>
        [
            'streetAddress' => '2 chemin des Vergers',
            'postalCode'    => '49170',
        ],
    ],
    Person::class
);

// $person->address is now a fully-typed PostalAddress instance.
```

`hydrate()` walks the public properties of the target class, resolves union types and instantiates nested value objects as it goes.

---

## 4. JSON-LD serialization

`Thing` implements `JsonSerializable`, so `json_encode()` produces valid JSON-LD out of the box. Two synthetic keys are injected automatically:

- `@type`    — the short class name of the entity.
- `@context` — the constant `Thing::CONTEXT` (defaults to `https://schema.org`; subclasses can override it, e.g. `Pagination::CONTEXT = 'https://schema.oihana.xyz'`).

Null properties are stripped from the output, keeping payloads compact.

```php
use org\schema\Person;
use org\schema\PostalAddress;

$person = new Person
([
    'id'      => '2555',
    'name'    => 'John Doe',
    'address' => new PostalAddress
    ([
        'streetAddress' => '2 chemin des Vergers',
        'postalCode'    => '49170',
    ]),
]);

echo json_encode( $person , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

Output:

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

---

## 5. Safe property access with constants

Hard-coding property names as strings is error-prone. The library ships a `Schema` aggregator class that exposes every Schema.org property as a typed `public const string`:

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
        Schema::POSTAL_CODE    => '49170',
    ]),
]);
```

Constants are grouped into per-type traits under `org\schema\constants\traits` (`Thing`, `Person`, `Event`, …) and composed into the `Schema` class — IDE auto-completion is therefore always topic-aware.

The same pattern is used by every extension namespace:

- `xyz\oihana\schema\constants\Oihana` — Oihana-specific extensions.
- `com\progress\schema\constants\Progress` — Progress OpenEdge SQL catalog.

---

## 6. Where to go next

- 📖 Browse the full [API reference](../../docs) generated by phpDocumentor.
- 🇫🇷 Explore the `xyz\oihana` namespace for domain-specific extensions (pagination, auth, places, …) — see [`README.md`](../../README.md#-xyzoihana-namespace-oihanaxyz-extensions).
- 🗄️ Map an OpenEdge database with the `com\progress` namespace — `Table`, `Column`, `Index`, `User`, `View`, `TableAuth`, `Sequence`, `Trigger`, constraints and more.
- 🧮 Generate JSON Schemas from your typed classes:

  ```bash
  composer schemas:all
  ```

- ✅ Run the test suite to verify your setup:

  ```bash
  composer test
  ```
